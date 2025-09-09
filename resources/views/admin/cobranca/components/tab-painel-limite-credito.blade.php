<div class="card-body p-2">
    <div class="table-responsive col-12 col-md-6">
        <div class="table-responsive">
            <table id="tabela-limite-credito" class="table compact table-font-small table-striped table-bordered nowrap"
                style="width:100%;">
            </table>
        </div>
    </div>
</div>

@section('css')

    <style>
        #tabela-limite-credito {
            font-size: 10px;
        }

        @media (max-width: 768px) {
            #tabela-limite-credito {
                font-size: 11px;
            }
        }
    </style>


@stop
