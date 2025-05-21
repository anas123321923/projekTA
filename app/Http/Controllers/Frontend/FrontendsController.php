<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{kamar,provinsi,Testimoni,User,SimpanKamar, Promo};
use Auth;
use Carbon\carbon;

class FrontendsController extends Controller
{
    // Homepage
    public function homepage(Request $request)
    {
      $cari = $request->cari;

      $kamar = kamar::with('promo')
      ->whereHas('provinsi', function($q) use ($cari) {
        $q->where('name', 'like', "%".$cari."%")
        ;
      })
      ->orwhereHas('regencies', function($q) use ($cari){
        $q->where('name', 'like', "%".$cari."%");
      })
      ->orwhereHas('district', function($q) use ($cari){
        $q->where('name', 'like', "%".$cari."%");
      })
      ->orwhere('nama_kamar', 'like', "%".$cari."%")
      ->orderBy('created_at','DESC')
      ->paginate(8);

      $promo = Promo::with('kamar')->where('status','1')->where('start_date_promo', '<=' ,carbon::now()->format('d F, Y'))->get();
      // return $promo;
      return view('front.index', \compact('kamar','promo'));
    }

    // Show Kamar
    public function showkamar($slug)
    {
      $kamar = kamar::with('province')
      ->with('promo', function($q){
        $q->where('status','1');
      })
      ->where('slug', $slug)->first();
      $fav = SimpanKamar::where('kamar_id',$kamar->id)->where('user_id',Auth::id())->first();
      $relatedKos = kamar::with('promo')->whereNotIn('slug', [$slug])
        ->where('province_id', [$kamar->province_id])
        ->limit(4)->get();

      return view('front.show', compact('kamar','relatedKos','fav'));
    }

    // Show semua kamar
    public function showAllKamar(Request $request)
    {
      $cari = $request->cari;
      $allKamar = kamar::with('promo')
      ->whereHas('provinsi', function($q) use ($cari) {

        $q->where('name', 'like', "%".$cari."%")
        ;
      })
      ->orwhereHas('regencies', function($q) use ($cari){
        $q->where('name', 'like', "%".$cari."%");
      })
      ->orwhereHas('district', function($q) use ($cari){
        $q->where('name', 'like', "%".$cari."%");
      })

      ->orwhereHas('favorite', function($q) use ($cari){
        $q->where('user_id', 'like', "%".$cari."%")
        ->where('user_id', Auth::id());
      })
      ->orwhere('nama_kamar', 'like', "%".$cari."%")
      ->orderBy('created_at','DESC')
      ->paginate(12);

      $provinsi = Kamar::with('provinsi','promo')->select('province_id')->groupby('province_id')->get();
      $select = [];
      $select['jenis_kamar'] = $request->jenis_kamar;
      $select['name']        = $request->nama_provinsi;
      $select['user_id']     = $request->user;
      return view('front.allCardContent', \compact('allKamar','select','provinsi','cari'));
    }

    // Filter kamar
    public function filterKamar(Request $request)
    {
      if ($request->nama_provinsi != 'all' && $request->jenis_kamar != 'all') {
        $allKamar = kamar::with('promo')->whereHas('provinsi', function($q) use ($request) {
          $q->where('name', $request->nama_provinsi);
        })
        ->where('jenis_kamar', $request->jenis_kamar)
        ->paginate(12);
      } elseif($request->nama_provinsi == 'all' && $request->jenis_kamar != 'all') {
        $allKamar = kamar::with('promo')->where('jenis_kamar', $request->jenis_kamar)->paginate(12);
      } elseif($request->nama_provinsi != 'all' && $request->jenis_kamar == 'all') {
          $allKamar = kamar::with('promo')->whereHas('provinsi', function($q) use ($request) {
          $q->where('name', $request->nama_provinsi);
        })
        ->orderBy('created_at','DESC')
        ->paginate(12);
      } else {
        $allKamar = kamar::with('promo')->orderBy('created_at','DESC')->paginate(12);
      }


      $select = [];
      $select['jenis_kamar'] = $request->jenis_kamar;
      $select['name']        = $request->nama_provinsi;

      // select provinsi
      $provinsi = Kamar::with('provinsi','promo')->select('province_id')->groupby('province_id')->get();
      return view('front.allCardContent', \compact('allKamar','select','provinsi'));
    }

    // Show by kota
    public function showByKota(Request $request)
    {
        $kota = str_replace('+', ' ', $request->kota);
        $district_id = null;
        
        // Mapping nama distrik ke ID
        $districtMapping = [
            'MANOKWARI BARAT' => '920101',
            'MANOKWARI TIMUR' => '920102',
            'MANOKWARI UTARA' => '920103',
            'MANOKWARI SELATAN' => '920104',
            'WARMARE' => '920105',
            'PRAFI' => '920106',
            'MASNI' => '920107',
            'SIDEY' => '920108'
        ];

        // Jika parameter kota ada, konversi ke district_id
        if ($request->has('kota')) {
            $district_id = $districtMapping[$kota] ?? null;
        }

        $kamar = Kamar::where(function($query) use ($district_id) {
            if ($district_id) {
                $query->where('district_id', $district_id);
            }
        })
        ->where('sisa_kamar', '>', 0)
        ->paginate(12);
        
        return view('front.showByKota', [
            'kamar' => $kamar,
            'district_id' => $district_id,
            'kota' => $kota
        ]);
    }

    // Simpan kamar
    public function simpanKamar(Request $request)
    {
      $simpan = new SimpanKamar;
      $simpan->user_id  = Auth::id();
      $simpan->kamar_id = $request->id;
      $simpan->save();

      return back();
    }

    // Hapus kamar disimpan
    public function hapusKamar(Request $request)
    {
      $hapus = SimpanKamar::find($request->id);
      $hapus->delete();

      return back();
    }

}
