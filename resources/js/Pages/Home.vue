<template>

</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import axios from "axios";

export default {
    props: ['options'],
    data(){
        return {
        }
    },

    created(){
        console.log(this.options)
    },

    methods: {
        logout(){
            Inertia.post(this.$root.route + "/users/logout", {},
			{
				onSuccess: (res) => {
				},
				orError: (err) => {
				}
			});
        },

        generateBill(){
            swal({
                title: "Are you sure to generate this bill ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((proceed) => {
                if (proceed) {
                    axios.post(this.$root.route + "/bills/generate", this.form)
                        .then(response => {
                            if(response.data.status == 422) {
                                if(!!response.data.errMessage) {
                                    this.errMessage = response.data.errMessage
                                } else {
                                    this.saveError = response.data.errors
                                }
                            } else {
                                var data = response.data.data

                                this.clientData = data

                                swal("Generate Bill", "You successfully generated bill.", "success");

                                this.saveError = null



                                // location.reload()
                            }
                        })
                }
            });

        },



    },
    watch: {

    }
}
</script>

<style scoped>


</style>
