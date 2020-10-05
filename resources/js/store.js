import Vue from 'vue';
import axios from 'axios'
import Vuex from 'vuex';


Vue.use(Vuex);

const store=new Vuex.Store({

	state:{

		permissions:[],
		// permissions:['view_app','view_customers','edit_customer','','delete_customer'],

		isLoaded:false,

		check:false,

		
	},//end of state

	getters:{

		permissions(state){
			return state.permissions;
		},//end of permissions
		isLoaded(state){

			return state.isLoaded;
		},

	},//end of getters

	mutations:{

		isLoaded(state,payload){
			//send value 'true' or 'false' for the payload
			state.isLoaded=payload
		},

		setPermissions(state,payload){

			state.permissions=payload
		},


	},//end of mutations

actions:{

		async getPermissions(context,payload){

			let {data} =   await axios.get('api/permissions/check/')

			context.commit('setPermissions',data.permissions)

			context.commit('isLoaded',true)
		}

		
	}//end of actions
});//end of Vuex.store


export default store

