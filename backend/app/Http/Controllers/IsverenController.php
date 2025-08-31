<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class IsverenController extends Controller
{
    public function index()
    {
        return response()->json(Employer::query()->latest()->paginate(20));
    }

    public function show($id)
    {
        $isveren = Employer::with('profil')->findOrFail($id);
        return response()->json($isveren);
    }

    public function isIlanlari($id)
    {
        $ilanlar = JobPost::where('isveren_id', $id)->latest()->paginate(20);
        return response()->json($ilanlar);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sirket_adi' => ['required','string','max:255'],
            'e_posta' => ['required','email','max:255', Rule::unique('isverenler','e_posta')],
            'sifre' => ['required','string','min:6'],
            'konum' => ['nullable','string','max:255'],
            'telefon' => ['nullable','string','max:50'],
            'hakkinda' => ['nullable','string'],
        ]);

        $data['sifre'] = Hash::make($data['sifre']);
        $isveren = Employer::create($data);

        return response()->json($isveren, 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'e_posta' => ['required','email'],
            'sifre' => ['required','string'],
        ]);

        $isveren = Employer::where('e_posta', $data['e_posta'])->first();
        if (!$isveren || !Hash::check($data['sifre'], $isveren->sifre)) {
            return response()->json(['message' => 'Kimlik bilgileri hatalı'], 401);
        }

        $isveren->api_token = Str::random(60);
        $isveren->save();

        return response()->json(['token' => $isveren->api_token]);
    }

    private function authEmployer(Request $request): ?Employer
    {
        $auth = $request->header('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return null;
        }
        $token = substr($auth, 7);
        if (!$token) return null;
        return Employer::where('api_token', $token)->first();
    }

    public function me(Request $request)
    {
        $isveren = $this->authEmployer($request);
        if (!$isveren) {
            return response()->json(['message' => 'Yetkisiz'], 401);
        }
        return response()->json($isveren);
    }

    public function updateMe(Request $request)
    {
        $isveren = $this->authEmployer($request);
        if (!$isveren) {
            return response()->json(['message' => 'Yetkisiz'], 401);
        }

        $data = $request->validate([
            'sirket_adi' => ['sometimes','string','max:255'],
            'e_posta' => ['sometimes','email','max:255', Rule::unique('isverenler','e_posta')->ignore($isveren->id)],
            'sifre' => ['sometimes','string','min:6'],
            'konum' => ['nullable','string','max:255'],
            'sektor' => ['nullable','string','max:255'],
            'website' => ['nullable','string','max:255'],
            'telefon' => ['nullable','string','max:50'],
            'hakkinda' => ['nullable','string'],
        ]);

        if (isset($data['sifre'])) {
            $data['sifre'] = Hash::make($data['sifre']);
        }

        $isveren->update($data);
        return response()->json($isveren);
    }

    public function update(Request $request, $id)
    {
        $isveren = Employer::findOrFail($id);
        $data = $request->validate([
            'sirket_adi' => ['sometimes','string','max:255'],
            'e_posta' => ['sometimes','email','max:255', Rule::unique('isverenler','e_posta')->ignore($isveren->id)],
            'sifre' => ['sometimes','string','min:6'],
            'konum' => ['nullable','string','max:255'],
            'telefon' => ['nullable','string','max:50'],
            'hakkinda' => ['nullable','string'],
        ]);

        if (isset($data['sifre'])) {
            $data['sifre'] = Hash::make($data['sifre']);
        }

        $isveren->update($data);
        return response()->json($isveren);
    }

    public function destroy($id)
    {
        $isveren = Employer::findOrFail($id);
        $isveren->delete();
        return response()->json(['message' => 'Silindi']);
    }
}
