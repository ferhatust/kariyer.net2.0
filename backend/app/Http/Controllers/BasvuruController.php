<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Candidate;
use Illuminate\Http\Request;

class BasvuruController extends Controller
{
    public function index()
    {
        return response()->json(JobApplication::with(['isIlani','kullanici'])->latest()->paginate(20));
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

    // Logged-in candidate's applications
    public function myIndex(Request $request)
    {
        $kullanici = $this->authCandidate($request);
        if (!$kullanici) return response()->json(['message' => 'Yetkisiz'], 401);
        $query = JobApplication::with(['isIlani','kullanici'])->where('kullanici_id', $kullanici->id)->latest();
        return response()->json($query->paginate(20));
    }

    public function show($id)
    {
        $basvuru = JobApplication::with(['isIlani','kullanici'])->findOrFail($id);
        return response()->json($basvuru);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'is_ilani_id' => ['required','exists:is_ilanlari,id'],
            'kullanici_id' => ['nullable','exists:kullanicilar,id'],
            'on_yazi' => ['nullable','string'],
            'durum' => ['nullable','in:basvuruldu,on_secildi,reddedildi,ise_alindi'],
        ]);

        // infer candidate from token if not provided
        if (empty($data['kullanici_id'])) {
            $kullanici = $this->authCandidate($request);
            if (!$kullanici) return response()->json(['message' => 'Yetkisiz'], 401);
            $data['kullanici_id'] = $kullanici->id;
        }

        if (empty($data['durum'])) {
            $data['durum'] = 'basvuruldu';
        }

        $basvuru = JobApplication::create($data);
        return response()->json($basvuru, 201);
    }

    public function update(Request $request, $id)
    {
        $basvuru = JobApplication::findOrFail($id);
        $data = $request->validate([
            'is_ilani_id' => ['sometimes','exists:is_ilanlari,id'],
            'kullanici_id' => ['sometimes','exists:kullanicilar,id'],
            'on_yazi' => ['nullable','string'],
            'durum' => ['nullable','in:basvuruldu,on_secildi,reddedildi,ise_alindi'],
        ]);

        $basvuru->update($data);
        return response()->json($basvuru);
    }

    public function destroy($id)
    {
        $basvuru = JobApplication::findOrFail($id);
        $basvuru->delete();
        return response()->json(['message' => 'Silindi']);
    }
}
