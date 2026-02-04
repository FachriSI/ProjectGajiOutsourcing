@extends('layouts.main')

@section('title', 'UMP')

@section('content')

<h3 class="mt-4">Update UMP</h3>
<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data" action="/update-ump/{{$data->id}}">
                            <!-- Input Date Range -->
        @csrf
            <div class="mb-3">
                <label for="ump" class="form-label">UMP/UMK {{ $data->lokasi }}</label>
                <input type="text" class="form-control uang" name="ump" id="ump" value="{{ $data->ump}}">
            </div>
    
            <div class="mb-3">
                <label for="tahun" class="form-label">Tahun</label>
                <select class="form-control" name="tahun" id="tahun">
                    @php
                        $currentYear = date('Y');
                        $startYear = 2022;
                        $endYear = $currentYear + 5;
                    @endphp
                    @for ($i = $startYear; $i <= $endYear; $i++)
                        <option value="{{ $i }}" {{ (isset($data->tahun) && $data->tahun == $i) ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <script>
            $(document).ready(function(){
                $('.uang').mask('000.000.000.000', {reverse: true});
            });
        </script>
@endsection
