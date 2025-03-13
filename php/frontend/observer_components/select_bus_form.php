<section class="select-bus-form">


    <div class="card bus-selection-container">
        <div class="card-body">
            <div class="form-group">
                <form class="select-bus-form">
                    <div class="form-item-group">


                        <div class="form-item bus-type">
                            <label for="busType">Select Bus:</label>
                            <select name="bus_id" class="form-control" id="select-bus">

                            </select>
                        </div>

                        <div class="form-item bus-type">
                            <label for="busType">Direction:</label>
                            <select name="direction" class="form-control" id="direction">
                                <option value="" disabled selected>Select Direction</option>
                                <option value="INBOUND">INBOUND</option>
                                <option value="OUTBOUND">OUTBOUND</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary form-control add-bus-btn" type="submit">
                        Select Bus
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<script>
    $(() => {

         const sessionData = {
            user_id: <?php echo json_encode($_SESSION['user_id'] ?? null); ?>,
        };

        const fetchBuses = async () => {
            try {
                const response = await $.ajax({
                    url: "http://127.0.0.1/school_bus_system/php/backend/bus_session.php/available-buses",
                    type: "GET",
                    dataType: "json"
                });

                populateBusSelect(response.data);
            } catch (error) {
                console.error("Error fetching buses:", error);
            }
        };

        const populateBusSelect = (buses) => {
            const $select = $("select[name='bus_id']");
            $select.empty(); // Clear existing options

            $select.append(`<option value="" disabled selected >Select a Bus</option>`); // Default option

            buses.forEach(bus => {
                $select.append(`<option value="${bus.id}">[${bus.bus_type}] ${bus.bus_name} - ${bus.plate_number}</option>`);
            });
        };

        fetchBuses();

        const createSessionParams = {
            conductor_id: sessionData.user_id,
        };
        // event selectors
        $(".select-bus-form").submit((e) => {
            e.preventDefault();
            const check = verify(['bus_id', 'direction']);

            console.log('chk:::', check);
            if (!check) {
                createSession(createSessionParams);
            }
        })

        const verify = (items) => {

            const hasError = false;

            items.forEach((item) => {
                const identifier = `select[name=${item}]`;
                const value = $(identifier).val();

                if (value === null) {
                    hasError = true;
                }
                createSessionParams[item] = value;
            });

            return hasError;
        }


        // AJAX

        const createSession = (sessionParams) => {
            $.ajax({
                url: "http://127.0.0.1/school_bus_system/php/backend/bus_session.php/create-session",
                type: "POST",
                data: sessionParams,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                dataType: "json",
                success: function (response) {
                    console.log("Success:", response);
                    alert("Bus created successfully!");
                    $(".select-bus-form")[0].reset(); // Clear form fields
                },
                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    alert("Failed to create bus.");
                }
            });
        }

    });
</script>