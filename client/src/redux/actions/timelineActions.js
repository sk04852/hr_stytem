import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";
import { toast } from "react-toastify";

export const postTimelineCommentSuccess = (data) => ({
  type: ActionTypes.POST_TIMELINE_COMMENTS_SUCCESS,
  payload: data,
});

export const postTimelineCommentProcessing = () => ({
  type: ActionTypes.POST_TIMELINE_COMMENTS_PROCESSING,
});

export const postTimelineCommentFailed = (error) => ({
  type: ActionTypes.POST_TIMELINE_COMMENTS_FAILED,
  payload: error,
});

export const postTimelineComment = (values, id, fetchData) => (dispatch) => {
  dispatch(postTimelineCommentProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/timelines/comment/store`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(postTimelineCommentSuccess(response.data));
      fetchData();
      toast.success(response.data.message, {
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
        dispatch(postTimelineCommentFailed(error.response.data));
        toast.error(error.response.data.message, {
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
