<template>
    <div>
        This is the home page. <Link href="/example">Example</Link>
    </div>
</template>


<script setup>


onMounted(() => {
  axios.get(route('posts.index'))
  .then(function (response) {
    // handle success
    console.log(response.data);  // Assuming the server responds with JSON
  })
  .catch(function (error) {
    // handle error
    console.error(error);
  })
  .finally(function () {
    // perform action in finally
  });
});

</script>
