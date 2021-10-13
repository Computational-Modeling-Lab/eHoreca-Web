<template>
    <div class="mt-5 px-1">
        <h1>New Vehicle</h1>
        <table class="data_table">
            <tr>
                <th>Type <span class="red-text">*</span></th>
                <td>
                    <select v-model="type">
                        <option disabled value="">Please select one</option>
                        <option value="Φ.Ι.Χ. Ανοικτό">Φ.Ι.Χ. Ανοικτό</option>
                        <option value="Φ.Ι.Χ. Κλειστό">Φ.Ι.Χ. Κλειστό</option>
                        <option class="Απορριμματοφόρο">Απορριμματοφόρο</option>
                        <option class="Πλυντήριο κάδων">Πλυντήριο κάδων</option>
                        <option class="Σάρωθρο">Σάρωθρο</option>
                        <option class="Φ.Ι.Χ Τρίκυκλο">Φ.Ι.Χ Τρίκυκλο</option>
                        <option class="Φ.Ι.Χ. Ανατρεπόμενο"
                            >Φ.Ι.Χ. Ανατρεπόμενο</option
                        >
                        <option class="Φ.Ι.Χ. Βυτιοφόρο"
                            >Φ.Ι.Χ. Βυτιοφόρο</option
                        >
                        <option class="Φ.Ι.Χ. Τράκτορας"
                            >Φ.Ι.Χ. Τράκτορας</option
                        >
                    </select>
                </td>
            </tr>
            <tr>
                <th>Plates <span class="red-text">*</span></th>
                <td>
                    <input
                        type="text"
                        name="plates"
                        id="plates"
                        v-model="plates"
                    />
                </td>
            </tr>
            <tr>
                <th>Make</th>
                <td>
                    <input type="text" name="make" id="make" v-model="make" />
                </td>
            </tr>
            <tr>
                <th>First year of license <span class="red-text">*</span></th>
                <td>
                    <input
                        type="number"
                        name="year"
                        id="year"
                        v-model="yearLicense"
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
                        v-model="taxableHp"
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
                        v-model="payload"
                    />
                </td>
            </tr>
            <tr>
                <th>Payload unit <span class="red-text">*</span></th>
                <td>
                    <select v-model="payloadUnit">
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
                <th>Municipality <span class="red-text">*</span></th>
                <td>
                    <select v-model="municipality">
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
                <th>Unity <span class="red-text">*</span></th>
                <td>
                    <select v-model="unity">
                        <option disabled value="">Please select one</option>
                        <option value="Διοίκησης">Διοίκησης</option>
                        <option value="Ηλεκτροφωτισμού">Ηλεκτροφωτισμού</option>
                        <option value="Καθαριότητας">Καθαριότητας</option>
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
                                value="true"
                                v-model="inService"
                            />
                            <label for="yes">Yes</label>
                        </div>
                        <div class="radio-container">
                            <input
                                type="radio"
                                id="no"
                                value="false"
                                v-model="inService"
                            />
                            <label for="no">No</label>
                        </div>
                    </fieldset>
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
            internalId: "",
            type: "",
            plates: "",
            make: "",
            yearLicense: "",
            taxableHp: "",
            payload: "",
            payloadUnit: "",
            municipality: "",
            unity: "",
            inService: true
        };
    },
    methods: {
        submitBtn() {
            $.ajax({
                url: `api/vehicles`,
                method: "POST",
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`
                },
                data: JSON.stringify({
                    internal_id: this.internalId,
                    type: this.type,
                    plates: this.plates,
                    make: this.make,
                    year_first_license: parseInt(this.yearLicense),
                    taxable_hp: parseInt(this.taxableHp),
                    payload: parseInt(this.payload),
                    payload_unit: this.payloadUnit,
                    municipality: this.municipality,
                    unity: this.unity,
                    in_service: this.inService
                }),
                dataType: "json",
                contentType: "application/json",
                success: res =>
                    (window.location.href = "/list?table=vehicles&page=1")
            });
        },
        cancelBtn() {
            window.location.href = "/list?table=vehicles&page=1"
        }
    }
};
</script>
