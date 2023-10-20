import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { toast } from "react-toastify";
import { baseURL } from "../../components/Shared/baseURL";

export const getJobSuccess = (data) => ({
  type: ActionTypes.GET_JOBS_SUCCESS,
  payload: data,
});

export const getJobProcessing = () => ({
  type: ActionTypes.GET_JOBS_PROCESSING,
});

export const getJobFailed = (error) => ({
  type: ActionTypes.GET_JOBS_FAILED,
  payload: error,
});

export const getAllJobs =
  (pageNumber, searchTerm, title, job_type, company, location, sorting) =>
  (dispatch) => {
    dispatch(getJobProcessing());
    const token = localStorage.getItem("token");
    return axios
      .get(
        `${baseURL}api/jobs?page=${pageNumber}&filter[search]=${searchTerm}&filter[title]=${
          title ? title : ""
        }&filter[job_type]=${job_type ? job_type : ""}&filter[company]=${
          company ? company : ""
        }&filter[location]=${location ? location : ""}&sort=${
          sorting !== null ? sorting : ""
        }`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      )
      .then((response) => dispatch(getJobSuccess(response.data)))
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(getJobFailed(error.response.data));
        }
      });
  };

export const getAllActiveJobsSuccess = (data) => ({
  type: ActionTypes.GET_ALL_ACTIVE_JOBS_SUCCESS,
  payload: data,
});

export const getAllActiveJobsProcessing = () => ({
  type: ActionTypes.GET_ALL_ACTIVE_JOBS_PROCESSING,
});

export const getAllActiveJobsFailed = (error) => ({
  type: ActionTypes.GET_ALL_ACTIVE_JOBS_FAILED,
  payload: error,
});

export const getAllActiveJobs = () => (dispatch) => {
  dispatch(getAllActiveJobsProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/jobs/active`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getAllActiveJobsSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getAllActiveJobsFailed(error.response.data));
      }
    });
};

export const getJobByIdSuccess = (data) => ({
  type: ActionTypes.GET_JOB_BY_ID_SUCCESS,
  payload: data,
});

export const getJobByIdProcessing = () => ({
  type: ActionTypes.GET_JOB_BY_ID_PROCESSING,
});

export const getJobByIdFailed = (error) => ({
  type: ActionTypes.GET_JOB_BY_ID_FAILED,
  payload: error,
});

export const getJobById = (id) => (dispatch) => {
  dispatch(getJobByIdProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/jobs/show/` + id,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => dispatch(getJobByIdSuccess(response.data)))
    .catch((error) => dispatch(getJobByIdFailed(error)));
};

export const getJobsApplicantsSuccess = (data) => ({
  type: ActionTypes.GET_JOBS_APPLICANTS_SUCCESS,
  payload: data,
});

export const getJobsApplicantsProcessing = () => ({
  type: ActionTypes.GET_JOBS_APPLICANTS_PROCESSING,
});

export const getJobsApplicantsFailed = (error) => ({
  type: ActionTypes.GET_JOBS_APPLICANTS_FAILED,
  payload: error,
});

export const getJobsApplicants =
  (id, sortingValue, candidateActionStatus) => (dispatch) => {
    dispatch(getJobsApplicantsProcessing());
    const token = localStorage.getItem("token");
    return axios
      .get(
        `${baseURL}api/jobs/${id}/candidates?&sort=${
          sortingValue === null || sortingValue === undefined
            ? ""
            : sortingValue
        }&filter[status]=${candidateActionStatus ? candidateActionStatus : ""}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      )
      .then((response) => dispatch(getJobsApplicantsSuccess(response.data)))
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(getJobsApplicantsFailed(error.response.data));
        }
      });
  };

export const addJobSuccess = (data) => ({
  type: ActionTypes.ADD_JOBS_SUCCESS,
  payload: data,
});
export const addJobUpdateSuccess = (data) => ({
  type: ActionTypes.ADD_JOBS_UPDATE_SUCCESS,
  payload: data,
});
export const addJobUpdateFailure = (data) => ({
  type: ActionTypes.ADD_JOBS_UPDATE_Failure,
  payload: data,
});

export const addJobProcessing = () => ({
  type: ActionTypes.ADD_JOBS_PROCESSING,
});

export const addJobFailed = (error) => ({
  type: ActionTypes.ADD_JOBS_FAILED,
  payload: error,
});

export const addJobs =
  (formData, setUploadFiles, setUploadVideoFiles, setErrors, history) =>
  (dispatch) => {
    dispatch(addJobProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/jobs`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(addJobSuccess(response.data));
        setUploadFiles([]);
        setUploadVideoFiles([]);
        setErrors({});
        history.push("/hr-jobs");
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
        if (error.response !== null) {
          dispatch(addJobFailed(error.response.data));
          setErrors(error.response.data.errors);
          toast.error(error.response.data.message || error.response.data.error, {
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

export const addUpdateJobActivity = (values, token, history) => (dispatch) => {
  dispatch(addJobProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/open/jobupdate`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(addJobUpdateSuccess(response));
      // history.push(`/hr-jobs`);
      // document.getElementById("add-job").reset();
      toast.success("Job Update Added To the Timeline", {
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
      if (error == null) {
        // console.log("success1");
      } else {
        // console.log(error);
        // console.log("error");
        dispatch(addJobUpdateFailure(error));
        toast.error("Job Update Not Added To Timeline", {
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

export const getjobdesirelangsuccess = (data) => ({
  type: ActionTypes.GET_DESIRED_JOBS_Lang_SUCCESS,
  payload: data,
});
export const getworklangsuccess = (data) => ({
  type: ActionTypes.GET_WORK_JOBS_Lang_SUCCESS,
  payload: data,
});
export const getshiftssuccess = (data) => ({
  type: ActionTypes.GET_SHIFTS_SUCCESS,
  payload: data,
});

export const getJobsActivitysuccess = (data) => ({
  type: ActionTypes.GET_JOBSACTIVITY_SUCCESS,
  payload: data,
});

export const getJobsCandidates = (data) => ({
  type: ActionTypes.GET_JOBSCANDIDATES_SUCCESS,
  payload: data,
});

export const getCandidateJobs = (id) => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/jobs/jobs_candidates/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getJobsCandidates(response)));
};

export const getJobsUpdate = () => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/jobupdate`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getJobSuccess(response)));
};

export const getJobsActivity = () => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/jobsactivity`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getJobsActivitysuccess(response)));
};

export const getJobsTimelineSuccess = (data) => ({
  type: ActionTypes.GET_JOBS_TIMELINE_SUCCESS,
  payload: data,
});

export const getJobsTimelineProcessing = () => ({
  type: ActionTypes.GET_JOBS_TIMELINE_PROCESSING,
});

export const getJobsTimelineFailed = (error) => ({
  type: ActionTypes.GET_JOBS_TIMELINE_FAILED,
  payload: error,
});

export const getJobsTimeline = (id) => (dispatch) => {
  dispatch(getJobsTimelineProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/jobs/timeline/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getJobsTimelineSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getJobsTimelineFailed(error.response.data));
      }
    });
};

export const updateJobSuccess = (data) => ({
  type: ActionTypes.UPDATE_JOBS_SUCCESS,
  payload: data,
});

export const updateJobProcessing = () => ({
  type: ActionTypes.UPDATE_JOBS_PROCESSING,
});

export const updateJobFailed = (error) => ({
  type: ActionTypes.UPDATE_JOBS_FAILED,
  payload: error,
});

export const updateJobs =
  (formData, setErrors, fetchJobByID, setUploadVideoFiles, history, id) =>
  (dispatch) => {
    dispatch(updateJobProcessing());

    const token = localStorage.getItem("token");

    return axios
      .post(`${baseURL}api/jobs/update`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(updateJobSuccess(response.data));
        fetchJobByID();
        setUploadVideoFiles([]);
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        setTimeout(() => {
          history.push(`/hr-jobs/view/${id}`);
        }, 2000);
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(updateJobFailed(error.response.data));
          setErrors(error.response.data.errors);
          toast.error(
            error.response.data.message || error.response.data.error,
            {
              position: "top-right",
              autoClose: 3000,
              hideProgressBar: true,
              closeOnClick: true,
              pauseOnHover: true,
              draggable: true,
              progress: undefined,
            }
          );
        }
      });
  };

export const deleteJobSuccess = (data) => ({
  type: ActionTypes.DELETE_JOBS_SUCCESS,
  payload: data,
});
export const deleteJobShiftsSuccess = (data) => ({
  type: ActionTypes.DELETE_JOBS_Shifts_SUCCESS,
  payload: data,
});
export const deleteWorkLangSuccess = (data) => ({
  type: ActionTypes.DELETE_Work_Lang_SUCCESS,
  payload: data,
});
export const deleteDesiredLangSuccess = (data) => ({
  type: ActionTypes.DELETE_Desired_Lang_SUCCESS,
  payload: data,
});

export const deleteJobProcessing = () => ({
  type: ActionTypes.DELETE_JOBS_PROCESSING,
});

export const deleteJobFailed = (error) => ({
  type: ActionTypes.DELETE_JOBS_FAILED,
  payload: error,
});

export const deleteJobs = (id, fetchAllJobs) => (dispatch) => {
  dispatch(deleteJobProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/jobs/delete`,
      {
        id: id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(deleteJobSuccess(response.data));
      // dispatch(getAllJobs(1));
      fetchAllJobs("", "", "", "");
      toast.success("Job Deleted Successfully", {
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
      dispatch(deleteJobFailed(error));
      toast.error("Job Deleted Failed", {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    });
};

export const deleteJobsShifts = (Job_id) => (dispatch) => {
  dispatch(deleteJobProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/jobs/shifts/delete`,
      {
        id: Job_id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(deleteJobShiftsSuccess(response));
      //   toast.success("Job Deleted Successfully", {
      //     position: "top-right",
      //     autoClose: 3000,
      //     hideProgressBar: true,
      //     closeOnClick: true,
      //     pauseOnHover: true,
      //     draggable: true,
      //     progress: undefined,
      //   });
    })
    .catch((error) => {
      dispatch(deleteJobFailed(error));
      //   toast.error("Job Deleted Failed", {
      //     position: "top-right",
      //     autoClose: 3000,
      //     hideProgressBar: true,
      //     closeOnClick: true,
      //     pauseOnHover: true,
      //     draggable: true,
      //     progress: undefined,
      //   });
    });
};
export const deleteWorkLang = (Job_id) => (dispatch) => {
  dispatch(deleteJobProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/jobs/workLang/delete`,
      {
        id: Job_id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      console.log("Success Work Lang Delete");
      dispatch(deleteWorkLangSuccess(response));
      //   toast.success("Job Deleted Successfully", {
      //     position: "top-right",
      //     autoClose: 3000,
      //     hideProgressBar: true,
      //     closeOnClick: true,
      //     pauseOnHover: true,
      //     draggable: true,
      //     progress: undefined,
      //   });
    })
    .catch((error) => {
      console.log(error);
      dispatch(deleteJobFailed(error));
      //   toast.error("Job Deleted Failed", {
      //     position: "top-right",
      //     autoClose: 3000,
      //     hideProgressBar: true,
      //     closeOnClick: true,
      //     pauseOnHover: true,
      //     draggable: true,
      //     progress: undefined,
      //   });
    });
};
export const deletedesirelang = (Job_id) => (dispatch) => {
  dispatch(deleteJobProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/jobs/desireLang/delete`,
      {
        id: Job_id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      console.log("Success desired lang delete");
      dispatch(deleteDesiredLangSuccess(response));
      //   toast.success("Job Deleted Successfully", {
      //     position: "top-right",
      //     autoClose: 3000,
      //     hideProgressBar: true,
      //     closeOnClick: true,
      //     pauseOnHover: true,
      //     draggable: true,
      //     progress: undefined,
      //   });
    })
    .catch((error) => {
      console.log(error);
      dispatch(deleteJobFailed(error));
      //   toast.error("Job Deleted Failed", {
      //     position: "top-right",
      //     autoClose: 3000,
      //     hideProgressBar: true,
      //     closeOnClick: true,
      //     pauseOnHover: true,
      //     draggable: true,
      //     progress: undefined,
      //   });
    });
};

export const deleteJobSingleVideoSuccess = (data) => ({
  type: ActionTypes.DELETE_JOB_SINGLE_VIDEO_SUCCESS,
  payload: data,
});

export const deleteJobSingleVideoProcessing = () => ({
  type: ActionTypes.DELETE_JOB_SINGLE_VIDEO_PROCESSING,
});

export const deleteJobSingleVideoFailed = (error) => ({
  type: ActionTypes.DELETE_JOB_SINGLE_VIDEO_FAILED,
  payload: error,
});

export const deleteJobSingleVideo = (id) => (dispatch) => {
  dispatch(deleteJobSingleVideoProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/jobs/file/delete/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(deleteJobSingleVideoSuccess(response.data));
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
      dispatch(deleteJobSingleVideoFailed(error));
      toast.error("Something went wrong. Please Try Again", {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    });
};

export const fetchBestMatchingCandidatesSuccess = (data) => ({
  type: ActionTypes.FETCH_BEST_MATCH_CANDIDATES_SUCCESS,
  payload: data,
});

export const fetchBestMatchingCandidatesProcessing = () => ({
  type: ActionTypes.FETCH_BEST_MATCH_CANDIDATES_PROCESSING,
});

export const fetchBestMatchingCandidatesFailed = (error) => ({
  type: ActionTypes.FETCH_BEST_MATCH_CANDIDATES_FAILED,
  payload: error,
});

export const fetchBestMatchingCandidates = (id) => (dispatch) => {
  dispatch(fetchBestMatchingCandidatesProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/jobs/find-best-match/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) =>
      dispatch(fetchBestMatchingCandidatesSuccess(response.data))
    )
    .catch((error) => dispatch(fetchBestMatchingCandidatesFailed(error)));
};

export const duplicateJobSuccess = (data) => ({
  type: ActionTypes.DUPLICATE_JOB_SUCCESS,
  payload: data,
});

export const duplicateJobProcessing = () => ({
  type: ActionTypes.DUPLICATE_JOB_PROCESSING,
});

export const duplicateJobFailed = (error) => ({
  type: ActionTypes.DUPLICATE_JOB_FAILED,
  payload: error,
});

export const duplicateJob = (id, fetchAllJobs) => (dispatch) => {
  dispatch(duplicateJobProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/jobs/${id}/duplicate`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => {
      dispatch(duplicateJobSuccess(response.data));
      fetchAllJobs();
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
      dispatch(duplicateJobFailed(error));
      toast.success(error.response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    });
};

export const getVerificationEmailLinkSuccess = (data) => ({
  type: ActionTypes.GET_EMAIL_VERIFICATION_TEMPLATES_LINK_SUCCESS,
  payload: data,
});

export const getVerificationEmailLinkProcessing = () => ({
  type: ActionTypes.GET_EMAIL_VERIFICATION_TEMPLATES_LINK_PROCESSING,
});

export const getVerificationEmailLinkFailed = (error) => ({
  type: ActionTypes.GET_EMAIL_VERIFICATION_TEMPLATES_LINK_FAILED,
  payload: error,
});

export const getVerificationEmailLinkData = (id, templateID) => (dispatch) => {
  dispatch(getVerificationEmailLinkProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/mail/templates/${templateID}/verify-job/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) =>
      dispatch(getVerificationEmailLinkSuccess(response.data))
    )
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getVerificationEmailLinkFailed(error.response.data));
      }
    });
};
