function createToast(param) {
  return undefined;
}

const toast = createToast({
  position: 'top-right',
  timeout: 3000,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  progress: undefined,
})

export default toast
