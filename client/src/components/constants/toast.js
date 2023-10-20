import React from "react";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

const Toast = () => {
  return (
    <>
      <ToastContainer
        position="top-tight"
        autoClose={3000}
        hideProgressBar
        newestOnTop={false}
        closeOnClick
        rtl={false}
        pauseOnFocusLoss
        draggable
        pauseOnHover
        // style={{ width: "500px" }}
        style={{ width: "360px" }}
        theme="colored"
      />
    </>
  );
};

export default Toast;
