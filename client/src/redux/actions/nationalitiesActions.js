import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { toast } from "react-toastify";
import { baseURL } from "../../components/Shared/baseURL";

export const getNationalitiesSuccess = (data) => ({
  type: ActionTypes.GET_NATIONALITIES_SUCCESS,
  payload: data,
});

export const getNationalitiesProcessing = () => ({
  type: ActionTypes.GET_NATIONALITIES_PROCESSING,
});

export const getNationalitiesFailed = (error) => ({
  type: ActionTypes.GET_NATIONALITIES_FAILED,
  payload: error,
});

export const getNationalities = () => (dispatch) => {
  dispatch(getNationalitiesProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/nationalities`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getNationalitiesSuccess(response.data));
      // toast.success(response.data.message, {
      //   position: "top-center",
      //   autoClose: 3000,
      //   hideProgressBar: false,
      //   closeOnClick: true,
      //   pauseOnHover: true,
      //   draggable: true,
      //   progress: undefined,
      // });
    })
    .catch((error) => {
      dispatch(getNationalitiesFailed(error.response));
      // toast.success(error.response.data.message, {
      //   position: "top-center",
      //   autoClose: 3000,
      //   hideProgressBar: false,
      //   closeOnClick: true,
      //   pauseOnHover: true,
      //   draggable: true,
      //   progress: undefined,
      // });
    });
};
