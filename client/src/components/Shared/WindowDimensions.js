export const getWindowDimensions = () => {
  const { innerWidth: width, innerHeight: height } = window;
  return { width, height };
};

export const screenHeight = window.innerHeight;
