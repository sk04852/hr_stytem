import * as ActionTypes from "../actionTypes";
import axios from "axios";
import {baseURL} from "../../components/Shared/baseURL";


export const getStatusSuccess = (data) => ({
  type: ActionTypes.GET_STATUS,
  payload: data,
});

export const getStatus = () => (dispatch) => {
    const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/actions`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getStatusSuccess(response)));
};
