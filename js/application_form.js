const { createApp, ref } = Vue;

createApp({
  setup() {
    const step = ref(1);
    const form = ref({
      name: '',
      email: '',
      phone: '',
      school: '',
      degree: '',
      gradYear: '',
      company: '',
      position: '',
      years: '',
      skills: '',
      resume: null
    });

    const handleResumeUpload = (e) => {
      form.value.resume = e.target.files[0];
    };

    const nextStep = () => {
      if (step.value < 4) {
        step.value++;
      } else {
        alert('Application Submitted!');
        console.log(form.value);
        // You can POST to PHP here
      }
    };

    return {
      step,
      form,
      nextStep,
      handleResumeUpload
    };
  }
}).mount('#app');