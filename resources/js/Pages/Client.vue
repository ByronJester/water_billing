<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2">
            View Client
        </div>
        
    </Navigation>
    
</template>

<script>
import Navigation from '../Layouts/Navigation.vue'

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
    },

    mounted() {
        console.log(this.options)
    }
}

</script>