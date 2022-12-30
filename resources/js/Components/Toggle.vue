<template>
  <div class="inline-flex">
    <slot class="mx-1">
    </slot>
    
    <div
      class="w-12 h-6 flex items-center bg-red-300 rounded-full p-1 duration-300 cursor-pointer"
      :class="{ 'bg-green-500': value }"
      :aria-checked="value"
      @click="toggle"
    >
      <div
        class="bg-white w-4 h-4 rounded-full shadow-md transform duration-300"
        :class="{ 'translate-x-6': value }"
      ></div>
    </div>
  </div>
</template>

<script>
import { Inertia } from "@inertiajs/inertia";

export default {
  props: ['value', 'url', 'id'],
  methods: {
    toggle() {
        swal({
            title: "Are you sure to this action?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((proceed) => {
            if (proceed) {
                this.$emit('value', !this.value)

                Inertia.post(this.$root.route + this.url, { is_active: !this.value, id: this.id },
                  {
                    onSuccess: (res) => {
                      swal({
                          title: 'Good Job!',
                          text: "Successfully",
                          icon: "success",
                          button: "Okay",
                      }).then( (proceed)=> {

                          location.reload()
                      });
                    },
                    onError: (err) => {
                    }
                });
            }
        });
        
    }
  }
};
</script>