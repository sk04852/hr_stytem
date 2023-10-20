import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { toast } from "react-toastify";
import { importFromFileBaseURL } from "../../components/Shared/baseURL";

export const importFromFileSuccess = (data) => ({
  type: ActionTypes.IMPORT_DATA_FROM_FILE_SUCCESS,
  payload: data,
});

export const importFromFileProcessing = () => ({
  type: ActionTypes.IMPORT_DATA_FROM_FILE_PROCESSING,
});

export const importFromFileFailed = (error) => ({
  type: ActionTypes.IMPORT_DATA_FROM_FILE_FAILED,
  payload: error,
});

export const importFromFile = (formValues, setFileLength) => (dispatch) => {
  dispatch(importFromFileProcessing());

  return axios
    .post(
      `${importFromFileBaseURL}ner`,
      {
        b64_string: formValues.b64_string,
      },
      {
        headers: {
          "Access-Control-Allow-Origin": "*",
          "Content-Type": "application/json",
        },
      }
    )
    .then((response) => {
      dispatch(importFromFileSuccess(response.data));
      setFileLength(1);
      toast.success("Import From Data Success", {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(importFromFileFailed(error.response));
        toast.error("Import From Data Failed", {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};
