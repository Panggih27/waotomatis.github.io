$(document).ready(function () {

    "use strict";

    $('#datatable1').DataTable();

    $('[name = "datatable1_length"]').removeClass([
      "form-control",
      "form-control-sm",
    ]);
    $('[name = "datatable1_length"]').addClass([
      "form-select",
      "form-select-sm",
    ]);

    $('#datatable2').DataTable({
        "scrollY": "300px",
        "scrollCollapse": true,
        "paging": false
    });

    $('#datatable3').DataTable({
        "scrollX": true
    });

    $('#datatable4 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#datatable4').DataTable({
        initComplete: function () {
            // Apply the search
            this.api().columns().every( function () {
                var that = this;
 
                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
        }
    });
});