define(['facebook'], function(FB){

  FB.init({
    appId      : 'APP ID',
  });

  FB.getLoginStatus(function(response) {
    console.log(response);
  });
  
  return FB;
});