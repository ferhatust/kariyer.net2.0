<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IsIlaniController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::with('isveren');

        // Free-text search across title/description/company
        if ($q = $request->input('q')) {
            $query->where(function ($qq) use ($q) {
                $qq->where('baslik', 'like', "%{$q}%")
                   ->orWhere('aciklama', 'like', "%{$q}%")
                   ->orWhere('sirket_adi', 'like', "%{$q}%");
            });
        }

        // Optional filters
        if ($konum = $request->input('konum')) {
            $query->where('konum', 'like', "%{$konum}%");
        }

        if (!is_null($request->input('uzaktan_mi'))) {
            $remote = filter_var($request->input('uzaktan_mi'), FILTER_VALIDATE_BOOLEAN);
            $query->where('uzaktan_mi', $remote);
        }

        if ($deneyim = $request->input('deneyim_duzeyi')) {
            $query->where('deneyim_duzeyi', $deneyim);
        }

        if ($isverenId = $request->input('isveren_id')) {
            $query->where('isveren_id', $isverenId);
        }

        $query->latest();

        $perPage = (int) $request->input('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        $ilan = JobPost::with('isveren')->findOrFail($id);
        return response()->json($ilan);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'isveren_id' => ['required','exists:isverenler,id'],
            'sirket_adi' => ['required','string','max:255'],
            'baslik' => ['required','string','max:255'],
            'aciklama' => ['required','string'],
            'konum' => ['nullable','string','max:255'],
            'uzaktan_mi' => ['boolean'],
            'maas' => ['nullable','numeric'],
            'para_birimi' => ['nullable','string','max:8'],
            'deneyim_duzeyi' => ['nullable','string','max:100'],
            'son_tarih' => ['nullable','date'],
        ]);

        $ilan = JobPost::create($data);
        return response()->json($ilan, 201);
    }

    public function update(Request $request, $id)
    {
        $ilan = JobPost::findOrFail($id);
        $data = $request->validate([
            'isveren_id' => ['sometimes','exists:isverenler,id'],
            'sirket_adi' => ['sometimes','string','max:255'],
            'baslik' => ['sometimes','string','max:255'],
            'aciklama' => ['sometimes','string'],
            'konum' => ['nullable','string','max:255'],
            'uzaktan_mi' => ['boolean'],
            'maas' => ['nullable','numeric'],
            'para_birimi' => ['nullable','string','max:8'],
            'deneyim_duzeyi' => ['nullable','string','max:100'],
            'son_tarih' => ['nullable','date'],
        ]);

        $ilan->update($data);
        return response()->json($ilan);
    }

    public function destroy($id)
    {
        $ilan = JobPost::findOrFail($id);
        $ilan->delete();
        return response()->json(['message' => 'Silindi']);
    }
}
