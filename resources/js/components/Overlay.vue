<template>
    <div class="overlay-wrapper">
        <!-- Overlay -->
        <div v-if="show" class="overlay">
            <div class="spinner-border text-primary"></div>
        </div>

        <!-- Wrapped content -->
        <div :class="{ 'overlay-content': true, 'overlay-blur': show }">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false,
        },
    },
};
</script>

<style scoped>
.overlay-wrapper {
    position: relative;
}

.overlay {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.overlay-blur {
    opacity: 0.5;
}
</style>
