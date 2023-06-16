{{-- <tbody> --}}
    @foreach ($inboxes as $inbox)
        <tr>
            <td class="text-center">
                {!! ! is_null($inbox->contact) ?  '<b>'.$inbox->contact->name.'</b>' : '+' . explode(':', explode('@',$inbox->sender)[0])[0] !!}
            </td>
            <td class="text-center fw-bold text-primary">
                {{ $inbox->body }}
            </td>
            <td class="text-center">{{ Carbon\Carbon::parse($inbox->created_at)->format('Y/m/d') }}</td>
            <td class="text-center">
                <div class="d-flex justify-content-center">
                    {{-- <button class="btn btn-sm btn-primary btn-edit me-1" data-id="{{ $cost->id }}" data-bs-toggle="modal" data-bs-target="#addPointCost">
                        Edit
                        <span class="material-icons-outlined" style="font-size: 15px !important;">mode_edit_outline</span>
                    </button> --}}
                    {{-- <button class="btn btn-sm btn-info mx-1 btn-detail" data-id="{{ $cost->id }}" data-bs-toggle="modal" data-bs-target="#detailCost">
                        Detail
                        <span class="material-icons-outlined" style="font-size: 15px !important;">visibility</span>
                    </button> --}}
                    <form action='{{ route("inbox.delete", ['number' => $inbox->number, 'id' => $inbox->id]) }}' method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger delete-cost" type="submit">
                            Delete
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
{{-- </tbody> --}}