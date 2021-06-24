<template>
  <div id="toggle-button">
    <!-- Child:{{tempStatus}} -->
    <label class="switch">
      <input type="checkbox" v-model="tempStatus" @change="changeStatus">
      <span class="slider round"></span>
    </label>
  </div>

<!-- 
  ** use this in parent component

                <td v-if="(invoice.status==='Paid')">
                       <toggle-button v-bind:status="true" @statusChanges =" tempStatus[invoice.id] = $event"/> 
                </td>
                
                <td v-else-if="(invoice.status==='To Pay')">
                       <toggle-button v-bind:status="false" @statusChanges =" tempStatus[invoice.id] = $event"/> 
                </td> 


                define data object  as "tempStatus:{}" which will hold value i.e status of button in object[key]; key can be invoice.id or any unique id to create multiple instance of chind compoent to get different value back from this child component

-->



</template>

<script>
export default {
  name: "ToggleButton",
  props: {
    status: Boolean,
  },

  data() {
    return {
      tempStatus: this.status
    };
  },
  methods: {
    changeStatus(event) {
      // this.tempStatus=event.target.value;
      this.$emit('statusChanges',this.tempStatus);
    },

  },



};
</script>

<style scoped>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #6bd098;
}

input:focus + .slider {
  box-shadow: 0 0 1px #6bd098;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}


/* Rounded sliders */

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
