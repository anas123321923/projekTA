<div class="card-btn d-flex justify-content-between mt-2">
    <h2 style="color:black">Kamu mungkin menyukainya</h2>
    <a href="{{ url('show-all-room') }}" class="btn btn-outline-info mb-1" style="color:black">Lihat Lainnya</a>
</div>

<div class="row match-height">
  @forelse ($relatedKos as $kamars)
    <div class="col-xl-3 col-md-6 col-sm-12">
      <div class="card">
        <div class="card-content">
          <a href="{{ url('room', $kamars->slug ?? '#') }}">
            <img 
              class="card-img-top img-fluid" 
              src="{{ asset('storage/images/bg_foto/' . ($kamars->bg_foto ?? 'default.jpg')) }}" 
              alt="Card image cap" 
              style="max-height: 180px"
            >
          </a>
          <div class="card-body">
            <a href="{{ url('room', $kamars->slug ?? '#') }}">
              <h5 style="min-height: 40px">
                {{ $kamars->nama_kamar ?? 'Nama kamar tidak tersedia' }} 
                {{ ucfirst(strtolower($kamars->regencies->name ?? 'Lokasi tidak tersedia')) }}
              </h5>
              <div class="d-flex justify-content-between">
                <a href="#" class="btn gradient-light-primary btn-sm">
                  {{ $kamars->jenis_kamar ?? 'Jenis kamar tidak tersedia' }}
                </a>
                <a href="#" class="btn btn-outline-{{ $kamars->sisa_kamar > 5 ? 'primary' : 'danger' }} btn-sm">
                  Tersisa {{ $kamars->sisa_kamar ?? '0' }} kamar
                </a>
              </div>
              <p class="card-text mt-1 mb-0">
                <i class="feather icon-map-pin"></i> 
                {{ $kamars->provinsi->name ?? 'Provinsi tidak tersedia' }}
              </p>
              <span class="card-text" style="color: rgb(96, 93, 93); text-decoration: line-through;">
                @if ($kamars->promo && $kamars->promo->status == 1 && 
                     $kamars->promo->start_date_promo <= Carbon\Carbon::now()->format('d F, Y'))
                  {{ rupiah($kamars->harga_kamar) }}
                @endif
              </span>
              <br>
              <span class="card-text" style="color: black">
                {{ rupiah(
                  $kamars->promo && $kamars->promo->status == 1 && 
                  $kamars->promo->start_date_promo <= Carbon\Carbon::now()->format('d F, Y')
                  ? $kamars->promo->harga_promo : $kamars->harga_kamar
                ) }} / Bulan
              </span>
            </a>
            <div class="card-btn d-flex justify-content-between mt-2">
              <a href="#" class="btn gradient-light-{{ $kamars->kategori == 'Kost' ? 'warning' : 'info' }} text-white btn-sm">
                {{ $kamars->kategori ?? 'Kategori tidak tersedia' }}
              </a>
              <a href="#" class="btn btn-outline-primary btn-sm {{ $kamars->book == 0 ? 'hidden' : '' }}">
                {{ $kamars->book == 0 ? 'Tidak Bisa Booking' : 'Bisa Booking' }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col" style="text-align:center">
      <img src="{{ asset('assets/images/pages/empty.svg') }}" style="max-height: 350px">
      <p class="mt-2">Tidak ada kamar lain di temukan!.</p>
    </div>
  @endforelse
</div>
