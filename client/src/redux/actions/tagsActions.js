import * as ActionTypes from "../actionTypes";
import axios from "axios";
import {baseURL} from "../../components/Shared/baseURL";

export const getLanguagesSuccess = (data) => ({
    type: ActionTypes.GET_TAGS_SUCCESS,
    payload: data,
});

export const getTags = () => (dispatch) => {
    const token = localStorage.getItem("token");
    return axios
        .get(`${baseURL}api/open/tags`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        })
        .then((response) => dispatch(getLanguagesSuccess(response)));
};
