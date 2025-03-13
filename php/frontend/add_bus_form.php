<?php


?>
<style>
    .form-item {
        margin: 15px;
    }

    .form-item label {
        margin-bottom: 10px;
    }

    .form-item-group {
        margin-bottom: 20px;
    }

    /* .add-bus-btn {
        margin: 10px;
    } */
</style>

<section class="container add-bus-form-container pt-5">

    <div class="add-bus-card">

        <div class="card">
            <div class="card-header">
                <h4 class="add-bus-header">Add Bus</h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <form class="add-bus-form">
                        <div class="form-item-group">
                            <div class="form-item bus-name">
                                <label for="busName">Bus Name:</label>
                                <input type="text" id="busName" name="bus_name" class="form-control">
                            </div>

                            <div class="form-item plate-number">
                                <label for="plateNumber">Plate Number:</label>
                                <input type="text" id="plateNumebr" name="plate_number" class="form-control">
                            </div>

                            <div class="form-item capacity">
                                <label for="capacity">Capacity:</label>
                                <input min="0" type="number" id="capacity" name="capacity" class="form-control">
                            </div>

                            <div class="form-item max-capacity">
                                <label for="maxCapacity">Maximum Capacity:</label>
                                <input min="0" type="number" id="maxCapacity" name="max_capacity" class="form-control">
                            </div>

                            <div class="form-item bus-type">
                                <label for="busType">Bus Type:</label>
                                <select name="bus_type" class="form-control" id="busType">
                                    <option value="" disabled selected>Select option</option>
                                    <option value="Mini Bus">Mini Bus</option>
                                    <option value="Service">Service</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary form-control add-bus-btn" type="submit">
                            <i class="fa fa-add"></i>Add Bus
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>
</section>

<script>
    $(() => {

        const items = ['bus_name', 'plate_number', 'capacity', 'max_capacity', 'bus_type'];

        const addBusParams = {};

        // EVENT LISTENERS
        $('.add-bus-form').submit((e) => {
            e.preventDefault();
            const check = verify();
            if (!check) {
                addBus(addBusParams);
            }

        })



        // UTIL FUNCTIONS

        const error = (input, message) => {
            $(input).parent().addClass('has-error');
        }

        const verify = () => {

            const hasError = false;

            items.forEach((item) => {
                const identifier = item === 'bus_type' ? `select[name=${item}]` : `input[name=${item}]`;
                const value = $(identifier).val();

                switch (item) {
                    case 'bus_name':
                    case 'plate_number':

                        if (value === '') {
                            hasError = true;
                            error(identifier, "This field should not be empty.")
                        }
                        break;
                    case 'capacity':
                    case 'max_capacity':

                        if (isNaN(value) || value === '') {
                            hasError = true;
                            error(identifier, "This field should be a proper number.")
                        }

                        break;

                }

                addBusParams[item] = value;
            });

            return hasError;
        }





        // AJAX/ API CALLS
        const fetchAllBuses = () => {
            $.ajax({
                url: "http://127.0.0.1/school_bus_system/php/backend/bus_session.php/available-buses",
                type: "GET",
                dataType: "json",
                success: function (response) {
                    console.log("Success:", response);
                    // Handle the response data here
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }


        const addBus = (busData) => {
            $.ajax({
                url: "http://127.0.0.1/school_bus_system/php/backend/bus_crud.php/buses/create",
                type: "POST",
                data: busData,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                dataType: "json",
                success: function (response) {
                    console.log("Success:", response);
                    alert("Bus created successfully!");
                    $(".add-bus-form")[0].reset(); // Clear form fields
                },
                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    alert("Failed to create bus.");
                }
            });
        }



    })
</script>