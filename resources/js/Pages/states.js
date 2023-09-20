
import { createApp } from 'vue'
import StateList from '@/components/ProjectComponents/Masters/State/StateList.vue'

const app = createApp({});
app.component('state-list', StateList);
app.mount('#app');
