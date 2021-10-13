<template>
    <table class="data_table">
        <tr>
            <th>Id</th>
            <td>{{ vehicle.id }}</td>
        </tr>
        <tr>
            <th>Type</th>
            <td>
                <select v-model="vehicle.type">
                    <option disabled value="">Please select one</option>
                    <option value="Φ.Ι.Χ. Ανοικτό">Φ.Ι.Χ. Ανοικτό</option>
                    <option value="Φ.Ι.Χ. Κλειστό">Φ.Ι.Χ. Κλειστό</option>
                    <option class="Απορριμματοφόρο">Απορριμματοφόρο</option>
                    <option class="Πλυντήριο κάδων">Πλυντήριο κάδων</option>
                    <option class="Σάρωθρο">Σάρωθρο</option>
                    <option class="Φ.Ι.Χ Τρίκυκλο">Φ.Ι.Χ Τρίκυκλο</option>
                    <option class="Φ.Ι.Χ. Ανατρεπόμενο">Φ.Ι.Χ. Ανατρεπόμενο</option>
                    <option class="Φ.Ι.Χ. Βυτιοφόρο">Φ.Ι.Χ. Βυτιοφόρο</option>
                    <option class="Φ.Ι.Χ. Τράκτορας">Φ.Ι.Χ. Τράκτορας</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Plates</th>
            <td>
                <input
                    type="text"
                    name="plates"
                    id="plates"
                    v-model="vehicle.plates"
                />
            </td>
        </tr>
        <tr>
            <th>Make</th>
            <td>
                <input
                    type="text"
                    name="make"
                    id="make"
                    v-model="vehicle.make"
                />
            </td>
        </tr>
        <tr>
            <th>First year of license</th>
            <td>
                <input
                    type="number"
                    name="year"
                    id="year"
                    v-model="vehicle['first year of license']"
                />
            </td>
        </tr>
        <tr>
            <th>Horsepower</th>
            <td>
                <input
                    type="number"
                    name="hp"
                    id="hp"
                    v-model="vehicle.horsepower"
                />
            </td>
        </tr>
        <tr>
                <th>Payload <span class="red-text">*</span></th>
                <td>
                    <input
                        type="number"
                        name="payload"
                        id="payload"
                        v-model="vehicle.payload"
                    />
                </td>
            </tr>
            <tr>
                <th>Payload unit <span class="red-text">*</span></th>
                <td>
                    <select v-model="vehicle.payload_unit">
                        <option disabled value="">Please select one</option>
                        <option value="litres">Litre</option>
                        <option value="millilitres">Mililitre</option>
                        <option value="cubic meters">Cubic Metre</option>
                        <option value="gallons">Gallons</option>
                        <option value="barrels">Barrels</option>
                        <option value="cubic feet">Cubic Feet</option>
                        <option value="cubic Inches">Cubic Inches</option>
                        <option value="pints">Pints</option>
                    </select>
                </td>
            </tr>
        <tr>
            <th>Municipality</th>
            <td>
                <select v-model="vehicle.municipality">
                    <option disabled value="">Please select one</option>
                    <option value="ΚΕΡΚΥΡΑΙΩΝ">ΚΕΡΚΥΡΑΙΩΝ</option>
                    <option value="ΜΕΛΙΤΕΙΕΩΝ">ΜΕΛΙΤΕΙΕΩΝ</option>
                    <option value="ΘΙΝΑΛΙΩΝ">ΘΙΝΑΛΙΩΝ</option>
                    <option value="ΦΑΙΑΚΩΝ">ΦΑΙΑΚΩΝ</option>
                    <option value="ΕΣΠΕΡΙΩΝ">ΕΣΠΕΡΙΩΝ</option>
                    <option value="ΠΑΡΕΛΙΩΝ">ΠΑΡΕΛΙΩΝ</option>
                    <option value="ΑΧΙΛΛΕΙΩΝ">ΑΧΙΛΛΕΙΩΝ</option>
                    <option value="ΚΑΣΣΩΠΑΙΩΝ">ΚΑΣΣΩΠΑΙΩΝ</option>
                    <option value="ΠΑΛΑΙΟΚΑΣΤΡΙΤΩΝ">ΠΑΛΑΙΟΚΑΣΤΡΙΤΩΝ</option>
                    <option value="ΛΕΥΚΙΜΜΑΙΩΝ">ΛΕΥΚΙΜΜΑΙΩΝ</option>
                    <option value="ΑΓ. ΓΕΩΡΓΙΟΥ">ΑΓ. ΓΕΩΡΓΙΟΥ</option>
                    <option value="ΚΟΡΙΣΣΙΩΝ">ΚΟΡΙΣΣΙΩΝ</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>In service</th>
            <td>
                <fieldset>
                    <div class="radio-container">
                        <input
                            type="radio"
                            id="yes"
                            value="Yes"
                            v-model="vehicle['in service']"
                        />
                        <label for="yes">Yes</label>
                    </div>
                    <div class="radio-container">
                        <input
                            type="radio"
                            id="no"
                            value="No"
                            v-model="vehicle['in service']"
                        />
                        <label for="no">No</label>
                    </div>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th>Created at</th>
            <td>
                {{ vehicle["created at"] }}
            </td>
        </tr>
        <tr>
            <th>Updated at</th>
            <td>
                {{ vehicle["updated at"] }}
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
        vehicle: {
            type: Object | Array,
            required: true
        }
    }
};
</script>
