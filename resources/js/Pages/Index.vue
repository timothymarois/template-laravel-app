<template>
    <div>
        This is the home page. <Link href="/example">Example</Link>
    </div>
</template>


<script setup>


onMounted(() => {
  axios.get(route('posts.index'), {
    headers: {
      'Accept': 'application/json',  // Explicitly request JSON response
      'Content-Type': 'application/json'  // Set content type to JSON for consistency, usually more relevant for POST/PUT requests
    }
  })
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
