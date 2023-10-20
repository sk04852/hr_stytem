import React from "react";
import { ErrorMessage } from "formik";

const FieldErrorMessage = ({ fieldName }) => {
  return (
    <ErrorMessage name={fieldName}>
      {(msg) => <span style={{ color: "red" }}>{msg}</span>}
    </ErrorMessage>
  );
};

export default FieldErrorMessage;
