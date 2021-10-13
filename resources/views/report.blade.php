<!DOCTYPE html>
<html>

<head>
    @include('includes/head')
</head>

<body>
    <div class="st-container">
        @include('navbar');

        <div id="main-content" class="container">
            <div class="form-container" id="report-form-container">
                <div class="header-container text-center">
                    <h1>Make a report</h1>
                </div>
                <form action="">
                    <div class="form-group">
                        <label for="issue">Issue</label>
                        <select class="custom-select" id="issue" v-model="issue">
                            <option value="bin full" selected>Bin is full</option>
                            <option value="bin almost full">Bin is almost full</option>
                            <option value="bin damaged">Bin is damaged</option>
                            <option value="bin missing">Bin is missing</option>
                        </select>
                    </div>
                    <div class="input-group form-group">
                        <label for="comment">Comment</label>
                        <textarea class="form-control" name="comment" id="comment" rows="3" v-model="comment"></textarea>
                    </div>
                    <div class="button-container text-center">
                        <input type="submit" id="submit-report-button" value="Submit" @click.prevent="onClickSubmitHandler">
                    </div>
                </form>
            </div>
        </div>

        @include('footer')
        <script type="application/javascript">
            var app = new Vue({
                el: '#report-form-container',
                data: {
                    issue: 'bin full',
                    comment: '',
                    location_accuracy: 10,
                },
                methods: {
                    onClickSubmitHandler() {
                        if (this.issue === '') {
                            alert('Please select an issue'); // change into something more user friendly
                            return;
                        }

                        const urlParams = new URLSearchParams(window.location.search);
                        let data = {
                            bin_id: urlParams.get('bin'),
                            user_id: localStorage.getItem('userId'),
                            lat: urlParams.get('lat'),
                            lng: urlParams.get('lng'),
                            location_accuracy: this.location_accuracy,
                            issue: this.issue,
                            comment: this.comment !== '' ? this.comment : null,
                        }

                        if (urlParams.get('w_producer')) {
                            data.w_producer_id = urlParams.get('w_producer')
                            data.approved = 1
                        }

                        $.ajax({
                            url: 'api/reports',
                            method: 'POST',
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem("token")}`
                            },
                            data,
                            success: () => window.location.href = '/',
                            error: (err) => {
                                alert(err.responseJSON.message) // change into something more user friendly
                            }
                        })
                    }
                },
                mounted() {
                    console.log('mounted')
                }
            })
        </script>
        @include('includes/scripts', ['includeMap' => false])
    </div>
</body>

</html>
