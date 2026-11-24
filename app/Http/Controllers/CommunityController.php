<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function index(Request $request): View
    {
        return view('social', ['account' => $request->user()]);
    }

    private function members(int $id): Builder
    {
        $connections = DB::table('friendships')->where('requester_id', $id)
            ->selectRaw("addressee_id as peer, status, 'outgoing' as direction, created_at")
            ->unionAll(DB::table('friendships')->where('addressee_id', $id)
                ->selectRaw("requester_id as peer, status, 'incoming' as direction, created_at"));

        return DB::table('users as u')
            ->leftJoinSub($connections, 'link', 'link.peer', '=', 'u.id')
            ->where('u.id', '<>', $id)->where('u.status', 'active')->whereNull('u.deleted_at')
            ->whereNotIn('u.id', DB::table('blocks')->select('blocked_id')->where('blocker_id', $id))
            ->whereNotIn('u.id', DB::table('blocks')->select('blocker_id')->where('blocked_id', $id));
    }

    public function people(Request $request): JsonResponse
    {
        $data = $request->validate([
            'list' => ['sometimes', Rule::in(['discover', 'friends', 'incoming', 'outgoing', 'blocked'])],
            'q' => ['nullable', 'string', 'max:60'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:100000'],
        ]);
        $account = $request->user();
        abort_unless($account->status === 'active' && ! $account->deleted_at, 403);
        $base = $this->members($account->id);
        $friends = (clone $base)->where('link.status', 'accepted');
        $incoming = (clone $base)->where('link.status', 'pending')->where('link.direction', 'incoming');
        $outgoing = (clone $base)->where('link.status', 'pending')->where('link.direction', 'outgoing');
        $list = $data['list'] ?? 'discover';
        $query = match ($list) {
            'friends' => clone $friends,
            'incoming' => clone $incoming,
            'outgoing' => clone $outgoing,
            'blocked' => DB::table('blocks as b')->join('users as u', 'u.id', '=', 'b.blocked_id')
                ->where('b.blocker_id', $account->id)->whereNull('u.deleted_at')->orderByDesc('b.created_at'),
            default => (clone $base)->where(function (Builder $q) {
                $q->whereNull('link.status')->orWhere('link.status', 'rejected');
            }),
        };

        $term = trim(ltrim(trim($data['q'] ?? ''), '@'));
        if ($term !== '') {
            $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term).'%';
            $query->whereRaw("lower(u.username) like lower(?) escape '!'", [$pattern]);
        }
        if (in_array($list, ['incoming', 'outgoing'], true)) {
            $query->orderByDesc('link.created_at');
        }
        $query->select('u.id', 'u.username')->orderBy('u.username')->orderBy('u.id');
        if ($list !== 'blocked') {
            $query->leftJoin('profiles as p', 'p.user_id', '=', 'u.id')
                ->addSelect('u.profile_visible', 'p.display_name');
            if (Schema::hasColumn('users', 'avatar_settings')) {
                $query->addSelect('u.avatar_settings');
            }
        }
        $page = $query->paginate(8);
        $people = $page->getCollection()->map(function ($user) {
            $visible = $user->profile_visible ?? false;
            $avatar = json_decode($user->avatar_settings ?? '{}', true);
            return [
                'id' => $user->id,
                'username' => $user->username,
                'display' => $visible ? ($user->display_name ?: $user->username) : $user->username,
                'avatar' => $visible ? ($avatar['animal'] ?? null) : null,
            ];
        });
        return response()->json([
            'people' => $people, 'total' => $page->total(), 'pages' => $page->lastPage(),
            'counts' => ['friends' => $friends->count(), 'incoming' => $incoming->count(), 'outgoing' => $outgoing->count()],
        ])->header('Cache-Control', 'private, no-store');
    }

    public function update(Request $request): JsonResponse
    {
        $input = $request->validate([
            'person' => ['required', 'integer', 'min:1'],
            'action' => ['required', Rule::in(['send', 'accept', 'reject', 'cancel', 'remove', 'block', 'unblock'])],
        ]);
        $me = (int) $request->user()->getKey();
        $peer = (int) $input['person'];
        abort_if($me === $peer, 422);
        DB::transaction(function () use ($me, $peer, $input) {
            // Lock the same two rows in the same order for requests in either direction.
            $users = User::query()->whereIn('id', [$me, $peer])
                ->whereNull('deleted_at')->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            abort_unless($users->get($me)?->status === 'active', 404);
            $action = $input['action'];
            $pair = DB::table('friendships')->where('user_low_id', min($me, $peer))
                ->where('user_high_id', max($me, $peer));
            if ($action === 'unblock') {
                if (DB::table('blocks')->where('blocker_id', $me)->where('blocked_id', $peer)->delete()) {
                    // Older blocks may still have a friendship or invitation attached.
                    $pair->delete();
                }
                return;
            }
            abort_unless($users->get($peer)?->status === 'active', 404);
            if ($action === 'block') {
                $this->block($me, $peer, $pair);
                return;
            }
            $blocked = DB::table('blocks')->whereIn('blocker_id', [$me, $peer])
                ->whereIn('blocked_id', [$me, $peer])->whereColumn('blocker_id', '<>', 'blocked_id')->exists();
            abort_if($blocked, 404);
            $link = $pair->first();
            if ($action === 'send') {
                abort_if($link && $link->status !== 'rejected', 409);
                $values = ['requester_id' => $me, 'addressee_id' => $peer, 'status' => 'pending',
                    'created_at' => now(), 'responded_at' => null];
                if ($link) {
                    $pair->update($values);
                } else {
                    DB::table('friendships')->insert($values);
                }
                return;
            }
            abort_unless($link, 409);
            if ($action === 'accept' || $action === 'reject') {
                abort_unless($link->status === 'pending' && (int) $link->addressee_id === $me, 409);
                $pair->update(['status' => $action === 'accept' ? 'accepted' : 'rejected', 'responded_at' => now()]);
                return;
            }
            $canDelete = $action === 'remove' ? $link->status === 'accepted'
                : $link->status === 'pending' && (int) $link->requester_id === $me;
            abort_unless($canDelete, 409);
            $pair->delete();
        }, 3);
        return response()->json(['ok' => true]);
    }

    private function block(int $me, int $peer, Builder $pair): void
    {
        $keys = ['blocker_id' => $me, 'blocked_id' => $peer];
        if (! DB::table('blocks')->where($keys)->exists()) {
            abort_if(DB::table('blocks')->where('blocker_id', $peer)->where('blocked_id', $me)->exists(), 404);
            DB::table('blocks')->insert($keys);
        }
        $pair->delete();
    }
}
