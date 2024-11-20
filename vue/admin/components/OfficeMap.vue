<script setup lang="ts">
import {onMounted, ref} from 'vue';

interface OfficeMapProps {
  xInputName: string
  yInputName: string
  backgroundImage: string
  xCoordinate: number
  yCoordinate: number
}

const props = defineProps<OfficeMapProps>();

const marker = ref(null);
const officeMap = ref(null);
const xCoord = ref(props.xCoordinate);
const yCoord = ref(props.yCoordinate);
const isDragging = ref(false);

onMounted(() => {
  officeMap.value.addEventListener('mousemove', (event) => {
    if (isDragging.value) {
      xCoord.value += event.movementX;
      yCoord.value += event.movementY;

      xCoord.value = Math.max(0, xCoord.value);
      xCoord.value = Math.min(xCoord.value, officeMap.value.offsetWidth - marker.value.offsetWidth);

      yCoord.value = Math.max(0, yCoord.value);
      yCoord.value = Math.min(yCoord.value, officeMap.value.offsetHeight - marker.value.offsetHeight);

      if (Math.abs(event.offsetX - xCoord.value) > marker.value.offsetWidth && event.offsetX > marker.value.offsetWidth) {
        xCoord.value = event.offsetX;
      }
      if (Math.abs(event.offsetY - yCoord.value) > marker.value.offsetHeight && event.offsetY > marker.value.offsetHeight) {
        yCoord.value = event.offsetY;
      }
    }
  });

  document.addEventListener('mouseup', () => {
    isDragging.value = false;
  });
});
</script>

<template>
  <div class="wrapper">
    <div ref="officeMap" class="office-map" :style="{ backgroundImage: `url(${backgroundImage})` }">
      <span
          ref="marker"
          class="marker"
          :style="{ left: xCoord + 'px', top: yCoord + 'px' }"
          @mousedown.prevent="isDragging = true"
      >
      </span>
    </div>

    <input type="hidden" :name="xInputName" :value="xCoord"/>
    <input type="hidden" :name="yInputName" :value="yCoord"/>
  </div>
</template>

<style scoped>
.wrapper {
  position: relative;
}

.office-map {
  position: relative;
  width: 100%;
  height: 620px;
  background-size: cover;
  background-position: center;
}

.marker {
  width: 20px;
  height: 20px;
  background-color: red;
  position: absolute;
  cursor: pointer;
  border-radius: 50%;
}
</style>
