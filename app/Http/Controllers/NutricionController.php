<?php

namespace App\Http\Controllers;

use App\Models\Comida;
use App\Models\RestriccionUsuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NutricionController extends Controller
{
    /* ==========================================================
       COMIDAS
       ========================================================== */

    // GET /nutricion/comidas?fecha=2026-11-24
    public function listarComidas(Request $request)
    {
        $request->validate(['fecha' => 'required|date_format:Y-m-d']);
        $usuarioId = $request->user()->id;

        $comidas = Comida::with(['alimentos', 'fotos'])
            ->where('user_id', $usuarioId)
            ->whereDate('fecha', $request->fecha)
            ->orderBy('hora')
            ->orderBy('id')
            ->get()
            ->map(fn ($comida) => $this->formatearComida($comida))
            ->values();

        return response()->json([
            'comidas' => $comidas,
            'racha'   => $this->calcularRacha($usuarioId),
        ]);
    }

    // POST /nutricion/comidas  (manual o con foto)
    // PUT  /nutricion/comidas/{id} usa este mismo método a través de actualizar()
    public function guardar(Request $request, ?int $id = null)
    {
        $usuarioId = $request->user()->id;
        $datos = $this->normalizarEntrada($request);

        $validador = Validator::make($datos, [
            'fecha'                 => 'required|date_format:Y-m-d',
            'tipo'                  => 'required|in:desayuno,almuerzo,cena,snack',
            'hora'                  => 'nullable|date_format:H:i',
            'descripcion'           => 'required_without:alimentos|nullable|string|max:120',
            'notas'                 => 'nullable|string|max:300',
            'origen'                => 'required|in:manual,foto',
            'alimentos'             => 'nullable|array|max:30',
            'alimentos.*.nombre'    => 'required|string|max:80',
            'alimentos.*.porcion'   => 'nullable|string|max:40',
            'alimentos.*.confianza' => 'nullable|numeric|between:0,1',
            'foto'                  => $id ? 'nullable|image|max:5120' : 'required_if:origen,foto|nullable|image|max:5120',
        ], [
            'descripcion.required_without' => 'Escribe una descripción o al menos un alimento.',
        ]);

        if ($validador->fails()) {
            return response()->json([
                'message' => 'Revisa los datos de la comida.',
                'errors'  => $validador->errors(),
            ], 422);
        }

        $v = $validador->validated();

        $comida = DB::transaction(function () use ($v, $usuarioId, $request, $id) {
            // Crear una comida nueva o buscar la existente (solo si es del usuario actual)
            $comida = $id
                ? Comida::where('user_id', $usuarioId)->findOrFail($id)
                : new Comida(['user_id' => $usuarioId]);

            $comida->fill([
                'fecha'       => $v['fecha'],
                'tipo'        => $v['tipo'],
                'hora'        => $v['hora'] ?? null,
                'descripcion' => $v['descripcion'] ?? null,
                'notas'       => $v['notas'] ?? null,
                'origen'      => $v['origen'],
            ])->save();

            // En una edición reemplazamos toda la lista de alimentos
            $comida->alimentos()->delete();
            foreach ($v['alimentos'] ?? [] as $alimento) {
                $comida->alimentos()->create([
                    'nombre'    => $alimento['nombre'],
                    'porcion'   => $alimento['porcion'] ?? null,
                    'confianza' => $alimento['confianza'] ?? null,
                ]);
            }

            // Guardar la foto si llegó una nueva
            if ($request->hasFile('foto')) {
                $ruta = $request->file('foto')->store("comidas/{$usuarioId}", 'public');
                $comida->fotos()->create(['ruta' => $ruta]);
            }

            return $comida;
        });

        return response()->json([
            'ok'     => true,
            'comida' => $this->formatearComida($comida->load(['alimentos', 'fotos'])),
        ], $id ? 200 : 201);
    }

    // PUT /nutricion/comidas/{id}
    public function actualizar(Request $request, int $id)
    {
        return $this->guardar($request, $id);
    }

    // DELETE /nutricion/comidas/{id}
    public function eliminar(Request $request, int $id)
    {
        $comida = Comida::with('fotos')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        // Borra primero los archivos de las fotos del disco
        foreach ($comida->fotos as $foto) {
            Storage::disk('public')->delete($foto->ruta);
        }

        // Alimentos y fotos se borran solos por el cascadeOnDelete
        $comida->delete();

        return response()->json(['ok' => true]);
    }

    /* ==========================================================
       FOTOS
       ========================================================== */

    // POST /nutricion/fotos/identificar
    public function identificarFoto(Request $request)
    {
        $request->validate(['foto' => 'required|image|max:5120']);

        // AQUÍ conectas tu servicio real de reconocimiento de imágenes.
        // Mientras tanto devolvemos una lista vacía: el usuario escribe a mano.
        // Formato que espera el JS:
        // [['nombre' => 'Arepa', 'porcion' => '1 unidad', 'confianza' => 0.82]]
        return response()->json(['alimentos' => []]);
    }

    /* ==========================================================
       RESTRICCIONES (alergias, intolerancias, evita)
       ========================================================== */

    // GET /nutricion/restricciones
    public function listarRestricciones(Request $request)
    {
        $restricciones = RestriccionUsuario::where('user_id', $request->user()->id)
            ->orderBy('texto')
            ->get(['id', 'texto', 'tipo']);

        return response()->json(['restricciones' => $restricciones]);
    }

    // POST /nutricion/restricciones
    public function guardarRestriccion(Request $request)
    {
        $v = $request->validate([
            'texto' => 'required|string|min:3|max:60',
            'tipo'  => 'required|in:alergia,intolerancia,evita',
        ]);

        $usuarioId = $request->user()->id;

        // Evita duplicados sin importar mayúsculas
        $existe = RestriccionUsuario::where('user_id', $usuarioId)
            ->whereRaw('LOWER(texto) = ?', [mb_strtolower($v['texto'])])
            ->exists();

        if ($existe) {
            return response()->json(['message' => 'Ya tienes esa restricción registrada.'], 422);
        }

        $restriccion = RestriccionUsuario::create([
            'user_id' => $usuarioId,
            'texto'   => $v['texto'],
            'tipo'    => $v['tipo'],
        ]);

        return response()->json(['restriccion' => $restriccion], 201);
    }

    // DELETE /nutricion/restricciones/{id}
    public function eliminarRestriccion(Request $request, int $id)
    {
        RestriccionUsuario::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail()
            ->delete();

        return response()->json(['ok' => true]);
    }

    /* ==========================================================
       AYUDANTES PRIVADOS
       ========================================================== */

    // El JS manda "alimentos" como texto JSON cuando hay foto (FormData).
    // Aquí lo convertimos a arreglo para validarlo igual que el manual.
    private function normalizarEntrada(Request $request): array
    {
        $datos = $request->all();

        if (isset($datos['alimentos']) && is_string($datos['alimentos'])) {
            $decodificado = json_decode($datos['alimentos'], true);
            $datos['alimentos'] = is_array($decodificado) ? $decodificado : [];
        }

        return $datos;
    }

   // Convierte el modelo al formato que espera el JS
private function formatearComida(Comida $comida): array
{
    $foto = $comida->fotos->first();

    return [
        'id'          => $comida->id,
        'tipo'        => $comida->tipo,
        'hora'        => $comida->hora ? substr($comida->hora, 0, 5) : null,
        'descripcion' => $comida->descripcion,
        'notas'       => $comida->notas,
        'origen'      => $comida->origen,
        'alimentos'   => $comida->alimentos->map(fn ($a) => [
            'nombre'  => $a->nombre,
            'porcion' => $a->porcion,
        ])->values(),
        // La foto queda en storage/app/public y es accesible en /storage gracias a storage:link
        'foto_url'    => $foto ? asset('storage/' . $foto->ruta) : null,
        'nutricion'   => null, // aquí irán datos de un catálogo con fuente fiable
    ];
}

    // Días seguidos con al menos un registro, contando desde hoy
    // (o desde ayer si hoy todavía no tiene registros)
    private function calcularRacha(int $usuarioId): int
    {
        $fechas = Comida::where('user_id', $usuarioId)
            ->select('fecha')
            ->distinct()
            ->pluck('fecha')
            ->map(fn ($f) => Carbon::parse($f)->toDateString());

        $dia = now()->startOfDay();

        if (! $fechas->contains($dia->toDateString())) {
            $dia = $dia->subDay();
        }

        $racha = 0;
        while ($fechas->contains($dia->toDateString())) {
            $racha++;
            $dia->subDay();
        }

        return $racha;
    }
}