if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js')
    // page reload is disabled because it happens even on first SW registration
    // .then(registration => {
    //   registration.addEventListener('updatefound', () => {
    //     const newWorker = registration.installing
    //
    //     newWorker.addEventListener('statechange', () => {
    //       if (newWorker.state === 'activated' && navigator.serviceWorker.controller) {
    //         location.reload()
    //       }
    //     })
    //   })
    // })
  })
}

// navigator.serviceWorker.getRegistrations().then(function (registrations) {
//   for (const registration of registrations) {
//     registration.unregister()
//   }
// })
