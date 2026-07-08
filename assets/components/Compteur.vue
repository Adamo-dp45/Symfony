<script setup>
    import { ref } from 'vue';

    const count = ref(0)
    const increment = () => {
        count.value++
    }
</script>

<template>
    <div class="lead">
        Vue
    </div>
    <!--
        {{ count }}
        <button class="btn btn-success" @click="increment">Incrémenté</button>
    -->
</template>