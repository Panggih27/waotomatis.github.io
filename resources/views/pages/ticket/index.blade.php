
<x-app-layout title="{{ __('Term a Condition') }}">

    <div class="app-content">
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="text" role="tabpanel" aria-labelledby="account-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex gap-2 w-100 justify-content-between mb-3">
                                            <h5 class="card-title">Tiket</h5>
                                            <button class="btn btn-primary btn-sm btn-add" data-bs-toggle="modal" data-bs-target="#ticketModal">Add Ticket</button>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="list-group">
                                                    @if(count($data))
                                                    @foreach($data as $x)
                                                        <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                                                        <img class="rounded-circle flex-shrink-0" src="{{ Auth::user()->avatar }}" class="rounded-circle" width="30" height="30" alt="{{ Auth::user()->name }}">
                                                        <div class="d-flex gap-2 w-100 justify-content-between">
                                                            <div>
                                                            <h6 class="mb-0">List group item heading</h6>
                                                            <p class="mb-0 opacity-75">Some placeholder content in a paragraph.</p>
                                                            </div>
                                                            <small class="opacity-50 text-nowrap">now</small>
                                                        </div>
                                                        </a>
                                                    @endforeach
                                                    @else
                                                        <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                                                        <div class="d-flex gap-2 w-100 justify-content-between">
                                                          <div>
                                                            <h6 class="mb-0">Warning!</h6>
                                                            <p class="mb-0 opacity-75">You Not Have Ticket.</p>
                                                          </div>
                                                        </div>
                                                      </a>
                                                    @endif
                                                  </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">Add Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="POST">
                <div class="modal-body">
                        <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Subject:</label>
                        <input type="text" class="form-control" id="recipient-name">
                        </div>
                        <div class="mb-3">
                        <label for="message-text" class="col-form-label">Description:</label>
                        <textarea class="form-control" id="message-text"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </form>
            </div>
        </div>
    </div>

</x-app-layout>
