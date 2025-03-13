<style>
    .bus-logs-table {
        margin: 20px;
    }

    th,
    td {
        padding: 20px;
        text-align: left;
    }

    .log-table-container {
        padding: 20px;
    }
</style>


<section class="bus-logs-section">
    <div class="card log-table-container">

        <table class="bus-logs-table">
            <thead>
                <tr>
                    <th>Plate Number</th>
                    <th>Bus Name</th>
                    <th>Bus Type</th>
                    <th>Direction</th>
                    <th>Conductor/Observer</th>
                    <th>Session Duration</th>
                    <th>Session Start</th>
                    <th>Session End</th>
                    <th>Session Status</th>
                </tr>
            </thead>
        </table>

    </div>
</section>

<script>
    $(() => {
        console.log('clock:::', moment().format('YYYY-MM-DD'));

        // AJAX

        const busTable = $('.bus-logs-table').DataTable({
            ajax: async function (data, callback, settings) {
                try {
                    const response = await $.ajax({
                        url: "http://127.0.0.1/school_bus_system/php/backend/log_reports.php/bus/logs",
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
                { data: "direction" },
                { data: "conductor_name" },
                {
                    data: null,
                    title: "Duration",
                    render: (data, type, row) => {
                        if (row.time_start && row.time_end) {
                            const timeStart = moment(row.time_start);
                            const timeEnd = moment(row.time_end);

                            if (timeStart.isValid() && timeEnd.isValid()) {
                                const duration = moment.duration(timeEnd.diff(timeStart));
                                const hours = duration.hours();
                                const minutes = duration.minutes();

                                if (hours > 0) {
                                    return `${hours}h ${minutes}m`;
                                } else {
                                    return `${minutes}m`;
                                }
                            }
                        }
                    }

                },
                {
                    data: "time_start",
                    render: (data, type, row) => {
                        return moment(data).format('MMMM DD, YYYY HH:mm a')
                    }
                },
                {
                    data: "time_end",
                    render: (data, type, row) => {
                        if (data !== null) {
                            return moment(data).format('MMMM DD, YYYY HH:mm a');
                        } else {
                            return '--';
                        }
                    }

                },
                { data: "session_status" }
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