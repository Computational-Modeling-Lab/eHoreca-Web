<template>
    <table class="data_table">
        <tr>
            <th>Id</th>
            <td>{{ user.id }}</td>
        </tr>
        <tr>
            <th>First name</th>
            <td>
                <input type="text" name="name" id="name" v-model="user.name" />
            </td>
        </tr>
        <tr>
            <th>Last name</th>
            <td>
                <input
                    type="text"
                    name="surname"
                    id="surname"
                    v-model="user.surname"
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
                    v-model="user.email"
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
                    v-model="user.username"
                />
            </td>
        </tr>
        <tr>
            <th>Role</th>
            <td>
                <select v-model="user.role">
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
            <th>Details</th>
            <td>
                <textarea
                    name="details"
                    id="details"
                    v-model="user.details"
                    cols="30"
                    rows="10"
                ></textarea>
            </td>
        </tr>
        <tr>
            <th>Created at</th>
            <td>
                {{ user["created_at"] }}
            </td>
        </tr>
        <tr>
            <th>Updated at</th>
            <td>
                {{ user["updated_at"] }}
            </td>
        </tr>
    </table>
</template>

<script>
export default {
    data() {
        return {};
    },
    props: {
        user: {
            type: Object | Array,
            required: true
        }
    },
    methods: {}
};
</script>
