<template>
    <div class="mt-5 px-1">
        <h1>New Heatmap</h1>
        <table class="data_table">
            <tr>
                <th>Title</th>
                <td>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        v-model="title"
                    />
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td>
                    <input
                        type="text"
                        name="description"
                        id="description"
                        v-model="description"
                    />
                </td>
            </tr>
            <tr>
                <th>
                    Valid from
                </th>
                <td>
                    <input type="date" id="valid_from" v-model="valid_from" />
                </td>
            </tr>
            <tr>
                <th>
                    Valid to
                </th>
                <td>
                    <input type="date" id="valid_to" v-model="valid_to" />
                </td>
            </tr>
        </table>

        <br>

        <button class="button text-white" @click="submitBtn">Submit</button>
        <button class="button text-white" @click="cancelBtn">Cancel</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            title: "",
            description: "",
            valid_from: new Date(),
            valid_to: new Date()
        };
    },
    methods: {
        submitBtn() {
            $.ajax({
                url: `api/heatmaps`,
                method: "POST",
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
                data: JSON.stringify({
                    title: this.title,
                    description: this.description,
                    valid_from: this.valid_from,
                    valid_to: this.valid_to,
                    user_id: localStorage.getItem("userId")
                }),
                dataType: "json",
                contentType: "application/json",
                success: res =>
                    (window.location.href = "/list?table=heatmaps&page=1")
            });
        },
        cancelBtn() {
            window.location.href = "/list?table=heatmaps&page=1"
        }
    }
};
</script>
