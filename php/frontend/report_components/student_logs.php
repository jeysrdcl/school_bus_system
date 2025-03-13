<style>
    .student-logs-table {
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


<section class="student-logs-section">
    <div class="card log-table-container">

        <table class="student-logs-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Status</th>
                    <th>Bus</th>
                    <th>Travel Duration</th>
                    <th>Pick Up Time</th>
                    <th>Drop Off Time</th>
                    <th>Observer/Conductor</th>
                </tr>
            </thead>
        </table>

    </div>
</section>

<script>
    $(() => {
        console.log('clock:::', moment().format('YYYY-MM-DD'));

        // AJAX
        const fetchStudentLogs = async () => {
            $(() => {
                $('.student-logs-table').DataTable({
                    ajax: async function (data, callback, settings) {
                        try {
                            const response = await $.ajax({
                                url: "http://127.0.0.1/school_bus_system/php/backend/log_reports.php/students/logs",
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
                        { data: "student_id" },
                        { data: "student_name" },
                        { data: "student_status" },
                        { data: "bus_name" },
                        {
                            data: null,
                            title: "Duration",
                            render: (data, type, row) => {
                                if (row.pick_up_time && row.drop_off_time) {
                                    const pickUpTime = moment(row.pick_up_time);
                                    const dropOffTime = moment(row.drop_off_time);

                                    if (pickUpTime.isValid() && dropOffTime.isValid()) {
                                        const duration = moment.duration(dropOffTime.diff(pickUpTime));
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
                            data: "pick_up_time",
                            render: (data, type, row) => {
                                return moment(data).format('MMMM DD, YYYY HH:mm a')
                            }
                        },
                        {
                            data: "drop_off_time",
                            render: (data, type, row) => {
                                return moment(data).format('MMMM DD, YYYY HH:mm a')
                            }

                        },
                        { data: "conductor_name" }
                    ],

                });
            });

        };


        fetchStudentLogs();
        // (async () => {
        //     const studentLogs = await fetchStudentLogs();
        //     console.log('logs:::', studentLogs);

        //     $('.student-logs-table').DataTable({
        //         ajax: studentLogs,
        //         columns: [
        //             {data: 'student_name'},
        //             {data: 'student_status'},
        //             {data: 'bus_name'},
        //             {data: 'pick_up_time'},
        //             {data: 'drop_off_time'},
        //             {data: 'conductor_name'},
        //         ]
        //     });
        // })();

    });

</script>