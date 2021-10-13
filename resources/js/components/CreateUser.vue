<template>
    <div class="mt-5 px-1">
        <table class="data_table">
            <tr>
                <th>First name</th>
                <td>
                    <input type="text" name="name" id="name" v-model="name" />
                </td>
            </tr>
            <tr>
                <th>Last name</th>
                <td>
                    <input
                        type="text"
                        name="surname"
                        id="surname"
                        v-model="surname"
                    />
                </td>
            </tr>
            <tr>
                <th>Email</th>
                <td>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        v-model="email"
                    />
                </td>
            </tr>
            <tr>
                <th>Username</th>
                <td>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        v-model="username"
                    />
                </td>
            </tr>
            <tr>
                <th>Role</th>
                <td>
                    <select v-model="role">
                        <option disabled value="">Please select a role</option>
                        <option value="public">Public</option>
                        <option value="w_producer">Large producer owner</option>
                        <option value="w_producer_employee"
                            >Large producer employee</option
                        >
                        <option value="admin">Administrator</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Password</th>
                <td>
                    <input id="password" type="password" class="form-control" placeholder="Password" v-model="password">
                </td>
            </tr>
            <tr>
                <th>Confirm Password</th>
                <td>
                    <input id="password" type="password" class="form-control" placeholder="Retype Password" v-model="passwordRepeat">
                </td>
            </tr>
            <tr>
                <th>Details</th>
                <td>
                    <textarea
                        name="details"
                        id="details"
                        v-model="details"
                        cols="30"
                        rows="10"
                    ></textarea>
                </td>
            </tr>
        </table>

        <br>

        <button class="button text-white" @click="submitBtn"
            :disabled="password == '' && name == '' && surname == '' && email == '' && role == ''">
            Submit
        </button>
        <button class="button text-white" @click="cancelBtn">Cancel</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name: "",
            surname: "",
            email: "",
            username: "",
            password: "",
            passwordRepeat: "",
            role: "",
            details: ""
        };
    },
    methods: {
        submitBtn() {
            if (this.password != this.passwordRepeat) {
                alert('The passwords do not match!');
                return;
            }

            $.ajax({
                url: `api/users`,
                method: "POST",
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
                data: JSON.stringify({
                    name: this.name,
                    surname: this.surname,
                    email: this.email,
                    username: this.username,
                    role: this.role,
                    details: this.details,
                    password: this.password
                }),
                dataType: "json",
                contentType: "application/json",
                success: res =>
                    (window.location.href = "/list?table=users&page=1")
            });
        },
        cancelBtn() {
            window.location.href = "/list?table=users&page=1"
        }
    }
};
</script>
