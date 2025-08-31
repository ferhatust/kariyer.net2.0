<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class KullaniciController extends Controller
{
    public function index()
    {
        return response()->json(Candidate::query()->latest()->paginate(20));
    }

    public function show($id)
    {
        $kullanici = Candidate::findOrFail($id);
        return response()->json($kullanici);
    }

    public function basvurular($id)
    {
        $basvurular = JobApplication::where('kullanici_id', $id)->latest()->paginate(20);
        return response()->json($basvurular);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ad' => ['required','string','max:100'],
            'soyad' => ['required','string','max:100'],
            'e_posta' => ['required','email','max:255', Rule::unique('kullanicilar','e_posta')],
            'sifre' => ['required','string','min:6'],
            'telefon' => ['nullable','string','max:50'],
            'okudugu_egitim' => ['nullable','string','max:255'],
            'eski_calistigi_yer' => ['nullable','string','max:255'],
            'deneyim_duzeyi' => ['nullable','string','max:100'],
        ]);

        $data['sifre'] = Hash::make($data['sifre']);
        $kullanici = Candidate::create($data);

        return response()->json($kullanici, 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'e_posta' => ['required','email'],
            'sifre' => ['required','string'],
        ]);

        $kullanici = Candidate::where('e_posta', $data['e_posta'])->first();
        if (!$kullanici || !Hash::check($data['sifre'], $kullanici->sifre)) {
            return response()->json(['message' => 'Kimlik bilgileri hatalı'], 401);
        }

        $kullanici->api_token = Str::random(60);
        $kullanici->save();

        return response()->json(['token' => $kullanici->api_token]);
    }

    private function authCandidate(Request $request): ?Candidate
    {
        $auth = $request->header('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return null;
        }
        $token = substr($auth, 7);
        if (!$token) return null;
        return Candidate::where('api_token', $token)->first();
    }

    public function me(Request $request)
    {
        $kullanici = $this->authCandidate($request);
        if (!$kullanici) {
            return response()->json(['message' => 'Yetkisiz'], 401);
        }
        return response()->json($kullanici);
    }

    public function updateMe(Request $request)
    {
        $kullanici = $this->authCandidate($request);
        if (!$kullanici) {
            return response()->json(['message' => 'Yetkisiz'], 401);
        }

        $data = $request->validate([
            'ad' => ['sometimes','string','max:100'],
            'soyad' => ['sometimes','string','max:100'],
            'e_posta' => ['sometimes','email','max:255', Rule::unique('kullanicilar','e_posta')->ignore($kullanici->id)],
            'sifre' => ['sometimes','string','min:6'],
            'telefon' => ['nullable','string','max:50'],
            'okudugu_egitim' => ['nullable','string','max:255'],
            'eski_calistigi_yer' => ['nullable','string','max:255'],
            'deneyim_duzeyi' => ['nullable','string','max:100'],
        ]);

        if (isset($data['sifre'])) {
            $data['sifre'] = Hash::make($data['sifre']);
        }

        $kullanici->update($data);
        return response()->json($kullanici);
    }

    public function update(Request $request, $id)
    {
        $kullanici = Candidate::findOrFail($id);
        $data = $request->validate([
            'ad' => ['sometimes','string','max:100'],
            'soyad' => ['sometimes','string','max:100'],
            'e_posta' => ['sometimes','email','max:255', Rule::unique('kullanicilar','e_posta')->ignore($kullanici->id)],
            'sifre' => ['sometimes','string','min:6'],
            'telefon' => ['nullable','string','max:50'],
            'okudugu_egitim' => ['nullable','string','max:255'],
            'eski_calistigi_yer' => ['nullable','string','max:255'],
            'deneyim_duzeyi' => ['nullable','string','max:100'],
        ]);

        if (isset($data['sifre'])) {
            $data['sifre'] = Hash::make($data['sifre']);
        }

        $kullanici->update($data);
        return response()->json($kullanici);
    }

    public function destroy($id)
    {
        $kullanici = Candidate::findOrFail($id);
        $kullanici->delete();
        return response()->json(['message' => 'Silindi']);
    }
}
