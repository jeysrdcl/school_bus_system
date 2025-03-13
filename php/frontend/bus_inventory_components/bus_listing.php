<style>
    .bus-logs-table {
        margin: 20px;
    }

    th,
    td {
        padding: 20px;
        text-align: left;
    }

    .bus-table-container {
        padding: 20px;
    }
</style>


<section class="bus-listing-section">
    <div class="card bus-table-container">

        <table class="bus-table">
            <thead>
                <tr>
                    <th>Plate Number</th>
                    <th>Bus Name</th>
                    <th>Bus Type</th>
                    <th>Capacity</th>
                    <th>Maximum Capacity</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table>

    </div>
</section>

<script>
    $(() => {
        console.log('clock:::', moment().format('YYYY-MM-DD'));

        // AJAX

        const busTable = $('.bus-table').DataTable({
            ajax: async function (data, callback, settings) {
                try {
                    const response = await $.ajax({
                        url: "http://127.0.0.1/school_bus_system/php/backend/bus_crud.php/buses",
                        type: "GET",
                        dataType: "json"
                    });

                    console.log("DEBUG Response:", response);
                    callback({ data: response.data });
                } catch (error) {
                    console.error("Error fetching student logs:", error);
                }
            },
            columns: [
                { data: "plate_number" },
                { data: "bus_name" },
                { data: "bus_type" },
                { data: "capacity" },
                { data: "max_capacity" },
                { data: "status" },
            ],

        });

        $('div.dataTables_filter input').off().on('keyup', function () {
            const searchTerm = this.value.trim();

            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                busTable.search(searchTerm).draw();
            }
        });


    });

</script>