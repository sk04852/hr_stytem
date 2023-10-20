import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { toast } from "react-toastify";
import { baseURL } from "../../components/Shared/baseURL";
import { getCandidateActions } from "./candidateActions_Actions";

export const addCandidateSuccess = (data) => ({
  type: ActionTypes.ADD_CANDIDATE_SUCCESS,
  payload: data,
});

export const addCandidateProcessing = () => ({
  type: ActionTypes.ADD_CANDIDATE_PROCESSING,
});

export const addCandidateFailed = (error) => ({
  type: ActionTypes.ADD_CANDIDATE_FAILED,
  payload: error,
});

export const addTimeline =
  (action_id, action_name, candidate_id, userpr_id, comments, job_id) =>
  (dispatch) => {
    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/timelines/adduser`,
        {
          action_ID: action_id,
          candidate_ID: candidate_id,
          userpr_ID: userpr_id,
          comments: comments,
          job_ID: job_id,
          action_name: action_name,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        }
      )
      .then((response) => {
        //add response if necessary
      });
  };

export const addCandidates =
  (
    formData,
    setCandidateID,
    setErrors,
    setFormValues,
    initialValues,
    setTabsId
  ) =>
  (dispatch) => {
    dispatch(addCandidateProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate_cv`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(addCandidateSuccess(response));
        setCandidateID(response.data.cadidatecv.id);
        document.getElementById("candidate-personal-data-form").reset();
        setFormValues(initialValues);
        setTimeout(() => {
          localStorage.removeItem("candidate-personal-data-form");
        }, 3000);
        setTabsId("2");
        setErrors({});
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
          dispatch(addCandidateFailed(error.response.data));
          setErrors(error.response.data.errors);
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

export const updateCandidatePersonalInformationSuccess = (data) => ({
  type: ActionTypes.ADD_CANDIDATE_PERSONAL_INFORMATION_SUCCESS,
  payload: data,
});

export const updateCandidatePersonalInformationProcessing = () => ({
  type: ActionTypes.ADD_CANDIDATE_PERSONAL_INFORMATION_PROCESSING,
});

export const updateCandidatePersonalInformationFailed = (error) => ({
  type: ActionTypes.ADD_CANDIDATE_PERSONAL_INFORMATION_FAILED,
  payload: error,
});

export const updateCandidatePersonalInformation =
  (
    formData,
    setEditProfile,
    id,
    setErrors,
    fetchCandidateProfile,
    setTriggerUpdate
  ) =>
  (dispatch) => {
    dispatch(updateCandidatePersonalInformationProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate_cv/update`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(updateCandidatePersonalInformationSuccess(response.data));
        fetchCandidateProfile();
        setEditProfile(false);
        setTriggerUpdate({});
        setErrors({});
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
          dispatch(updateCandidatePersonalInformationFailed(error.response));
          if (error.response.data.errors) {
            setErrors(error.response.data.errors);
          }
          toast.error(error.response.data.response, {
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

export const updateCandidateActionIDSuccess = (data) => ({
  type: ActionTypes.ADD_CANDIDATE_ACTION_ID_SUCCESS,
  payload: data,
});

export const updateCandidateActionIDProcessing = () => ({
  type: ActionTypes.ADD_CANDIDATE_ACTION_ID_PROCESSING,
});

export const updateCandidateActionIDFailed = (error) => ({
  type: ActionTypes.ADD_CANDIDATE_ACTION_ID_FAILED,
  payload: error,
});

export const updateCandidateActionID =
  (
    action_id,
    candidate_id,
    userpr_id,
    comments,
    job_id,
    action_name,
    // action_type = "",
    // body = ""
    pageNumber,
    searchTerm,
    gender,
    job_history,
    history,
    template_key,
    setJobModal
  ) =>
  (dispatch) => {
    dispatch(updateCandidateActionIDProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/candidate_cv/update_action`,
        {
          action_ID: action_id,
          candidate_ID: candidate_id,
          userpr_ID: userpr_id,
          comments: comments,
          job_ID: job_id,
          action_name: action_name,
          // action_type: action_type,
          // body: body,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        }
      )
      .then((response) => {
        dispatch(updateCandidateActionIDSuccess(response.data));
        dispatch(getAllCandidates(pageNumber, searchTerm, gender, job_history));
        setJobModal(false);
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
          if (template_key) {
            history.push(
              `/hr-candidate/view/${candidate_id}/send-email/${template_key}`,
              {
                update: true,
              }
            );
          }
        }, 3000);
      })
      .catch((error) => {
        dispatch(updateCandidateActionIDFailed(error));
        toast.error(error.response.data.message, {
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

export const candidatesApplyToJobSuccess = (data) => ({
  type: ActionTypes.ADD_CANDIDATE_ACTION_ID_SUCCESS,
  payload: data,
});

export const candidatesApplyToJobProcessing = () => ({
  type: ActionTypes.ADD_CANDIDATE_ACTION_ID_PROCESSING,
});

export const candidatesApplyToJobFailed = (error) => ({
  type: ActionTypes.ADD_CANDIDATE_ACTION_ID_FAILED,
  payload: error,
});

export const candidatesApplyToJob =
  (
    action_id,
    candidatecv_id,
    job_id,
    template_key,
    history,
    setTriggerUpdate,
    setErrors,
    handleFetchBestMatchingJobs,
    offerPage,
    setFetchAppliedJobsUpdate
  ) =>
  (dispatch) => {
    dispatch(candidatesApplyToJobProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/candidate_cv/apply-to-job`,
        {
          action_id: action_id,
          candidatecv_id: candidatecv_id,
          job_pr_id: job_id,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        }
      )
      .then((response) => {
        dispatch(candidatesApplyToJobSuccess(response.data));
        setTriggerUpdate({});
        setErrors({});
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        if (offerPage) {
          setFetchAppliedJobsUpdate({});
        }
        if (action_id === 2) {
          handleFetchBestMatchingJobs(candidatecv_id);
        } else {
          setTimeout(() => {
            if (template_key) {
              history.push(
                `/hr-candidate/view/${candidatecv_id}/send-email/${template_key}`,
                {
                  update: true,
                  backToCandidateList: "/hr-candidates",
                }
              );
            }
          }, 3000);
        }
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(candidatesApplyToJobFailed(error.response.data));
          toast.error(error.response.data.message, {
            position: "top-right",
            autoClose: 3000,
            hideProgressBar: true,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
          });
          setErrors(error.response.data.errors);
        }
      });
  };

// FOR JOBS CANDIDATES
export const candidatesApplyToJobForJobs =
  (
    action_id,
    candidatecv_id,
    job_id,
    template_key,
    history,
    setTriggerUpdate,
    fetchJobsApplicants,
    jobLocation
  ) =>
  (dispatch) => {
    dispatch(candidatesApplyToJobProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/candidate_cv/apply-to-job`,
        {
          action_id: action_id,
          candidatecv_id: candidatecv_id,
          job_pr_id: job_id,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        }
      )
      .then((response) => {
        dispatch(candidatesApplyToJobSuccess(response.data));
        fetchJobsApplicants();
        setTriggerUpdate({});
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
          if (template_key) {
            history.push(
              `/hr-candidate/view/${candidatecv_id}/send-email/${template_key}`,
              {
                update: true,
                jobCandidates: jobLocation,
              }
            );
          }
        }, 3000);
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(candidatesApplyToJobFailed(error.response.data));
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

export const rejectedCandidateActionSuccess = (data) => ({
  type: ActionTypes.REJECTED_CANDIDATE_ACTION_SUCCESS,
  payload: data,
});

export const rejectedCandidateActionProcessing = () => ({
  type: ActionTypes.REJECTED_CANDIDATE_ACTION_PROCESSING,
});

export const rejectedCandidateActionFailed = (error) => ({
  type: ActionTypes.REJECTED_CANDIDATE_ACTION_FAILED,
  payload: error,
});

export const rejectedCandidateAction =
  (candidate_id, userpr_id, job_id) => (dispatch) => {
    dispatch(rejectedCandidateActionProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/candidate_cv/reject_candidate`,
        {
          candidate_ID: candidate_id,
          userpr_ID: userpr_id,
          job_ID: job_id,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        }
      )
      .then((response) => {
        dispatch(rejectedCandidateActionSuccess(response.data));
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
        dispatch(rejectedCandidateActionFailed(error));
        toast.error("Update Candidate Failed.", {
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

export const addCandidateJobHistorySuccess = (data) => ({
  type: ActionTypes.ADD_CANDIDATE_JOB_HISTORY_SUCCESS,
  payload: data,
});

export const addCandidateJobHistoryProcessing = () => ({
  type: ActionTypes.ADD_CANDIDATE_JOB_HISTORY_PROCESSING,
});

export const addCandidateJobHistoryFailed = (error) => ({
  type: ActionTypes.ADD_CANDIDATE_JOB_HISTORY_FAILED,
  payload: error,
});

export const addCandidateJobHistory =
  (formData, setErrors, setTabsId) => (dispatch) => {
    dispatch(addCandidateJobHistoryProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate-job-history`, formData, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(addCandidateJobHistorySuccess(response));
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        setErrors({});
        setTabsId("3");
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(addCandidateJobHistoryFailed(error.response.data));
          setErrors(error.response.data.errors);
          toast.error("Candidate Added Failed.", {
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

export const addCandidateEducationSuccess = (data) => ({
  type: ActionTypes.ADD_CANDIDATE_EDUCATION_SUCCESS,
  payload: data,
});

export const addCandidateEducationProcessing = () => ({
  type: ActionTypes.ADD_CANDIDATE_EDUCATION_PROCESSING,
});

export const addCandidateEducationFailed = (error) => ({
  type: ActionTypes.ADD_CANDIDATE_EDUCATION_FAILED,
  payload: error,
});

export const addCandidateEducation =
  (formData, setErrors, setTabsId) => (dispatch) => {
    dispatch(addCandidateEducationProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate-education`, formData, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(addCandidateEducationSuccess(response.data));
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        setErrors({});
        setTabsId("4");
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(addCandidateEducationFailed(error.response.data));
          setErrors(error.response.data.errors);
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

export const getCandidateAgreementSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_AGREEMENT_SUCCESS,
  payload: data,
});

export const getCandidateAgreementProcessing = () => ({
  type: ActionTypes.GET_CANDIDATE_AGREEMENT_PROCESSING,
});

export const getCandidateAgreementFailed = (error) => ({
  type: ActionTypes.GET_CANDIDATE_AGREEMENT_FAILED,
  payload: error,
});

export const getCandidateAgreement = (id) => (dispatch) => {
  dispatch(getCandidateAgreementProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate-agreements/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getCandidateAgreementSuccess(response.data));
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getCandidateAgreementFailed(error.response.data));
      }
    });
};

export const addCandidateAgreementSuccess = (data) => ({
  type: ActionTypes.CREATE_CANDIDATE_AGREEMENT_SUCCESS,
  payload: data,
});

export const addCandidateAgreementProcessing = () => ({
  type: ActionTypes.CREATE_CANDIDATE_AGREEMENT_PROCESSING,
});

export const addCandidateAgreementFailed = (error) => ({
  type: ActionTypes.CREATE_CANDIDATE_AGREEMENT_FAILED,
  payload: error,
});

export const addCandidateAgreement =
  (formData, id, setModal, setErrors) => (dispatch) => {
    dispatch(addCandidateAgreementProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate-agreements`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(addCandidateAgreementSuccess(response.data));
        dispatch(getCandidateAgreement(id));
        setModal(false);
        setErrors({});
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
          dispatch(addCandidateAgreementFailed(error.response.data));
          if (error.response.data.errors) {
            setErrors(error.response.data.errors);
          }
          toast.error(error.response.data.error, {
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

export const deleteCandidateAgreementSuccess = (data) => ({
  type: ActionTypes.DELETE_CANDIDATE_AGREEMENT_SUCCESS,
  payload: data,
});

export const deleteCandidateAgreementProcessing = () => ({
  type: ActionTypes.DELETE_CANDIDATE_AGREEMENT_PROCESSING,
});

export const deleteCandidateAgreementFailed = (error) => ({
  type: ActionTypes.DELETE_CANDIDATE_AGREEMENT_FAILED,
  payload: error,
});

export const deleteCandidateAgreement = (id, candidateID) => (dispatch) => {
  dispatch(deleteCandidateAgreementProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/candidate-agreements/delete`,
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
      dispatch(deleteCandidateAgreementSuccess(response.data));
      dispatch(getCandidateAgreement(candidateID));
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
        dispatch(deleteCandidateAgreementFailed(error.response.data));
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

export const updateCandidateEducationSuccess = (data) => ({
  type: ActionTypes.UPDATE_CANDIDATE_EDUCATION_SUCCESS,
  payload: data,
});

export const updateCandidateEducationProcessing = () => ({
  type: ActionTypes.UPDATE_CANDIDATE_EDUCATION_PROCESSING,
});

export const updateCandidateEducationFailed = (error) => ({
  type: ActionTypes.UPDATE_CANDIDATE_EDUCATION_FAILED,
  payload: error,
});

export const updateCandidateEducation = (formData, setErrors) => (dispatch) => {
  dispatch(updateCandidateEducationProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/open/candidate-education/update`, formData, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => {
      dispatch(updateCandidateEducationSuccess(response));
      setErrors({});
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
        dispatch(updateCandidateEducationFailed(error.response.data));
        setErrors(error.response.data.errors);
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

export const updateCandidateAdditionalCoursesSuccess = (data) => ({
  type: ActionTypes.UPDATE_CANDIDATE_ADDITIONAL_COURSES_SUCCESS,
  payload: data,
});

export const updateCandidateAdditionalCoursesProcessing = () => ({
  type: ActionTypes.UPDATE_CANDIDATE_ADDITIONAL_COURSES_PROCESSING,
});

export const updateCandidateAdditionalCoursesFailed = (error) => ({
  type: ActionTypes.UPDATE_CANDIDATE_ADDITIONAL_COURSES_FAILED,
  payload: error,
});

export const updateCandidateAdditionalCourses =
  (formData, setErrors) => (dispatch) => {
    dispatch(updateCandidateAdditionalCoursesProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate-additional-course/update`, formData, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(updateCandidateAdditionalCoursesSuccess(response.data));
        setErrors({});
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
          dispatch(updateCandidateAdditionalCoursesFailed(error.response.data));
          setErrors(error.response.data.errors);
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

export const deleteCandidateAdditionalCoursesSuccess = (data) => ({
  type: ActionTypes.DELETE_CANDIDATE_ADDITIONAL_COURSES_SUCCESS,
  payload: data,
});

export const deleteCandidateAdditionalCoursesProcessing = () => ({
  type: ActionTypes.DELETE_CANDIDATE_ADDITIONAL_COURSES_PROCESSING,
});

export const deleteCandidateAdditionalCoursesFailed = (error) => ({
  type: ActionTypes.DELETE_CANDIDATE_ADDITIONAL_COURSES_FAILED,
  payload: error,
});

export const deleteCandidateAdditionalCourses = (id) => (dispatch) => {
  dispatch(deleteCandidateAdditionalCoursesProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/candidate-additional-course/delete`,
      {
        id: id,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    )
    .then((response) => {
      dispatch(deleteCandidateAdditionalCoursesSuccess(response.data));
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
      dispatch(deleteCandidateAdditionalCoursesFailed(error));
      toast.error(error.response.data.message, {
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

export const addAdditionalCoursesSuccess = (data) => ({
  type: ActionTypes.ADD_ADDITIONAL_COURSES_SUCCESS,
  payload: data,
});

export const addAdditionalCoursesProcessing = () => ({
  type: ActionTypes.ADD_ADDITIONAL_COURSES_PROCESSING,
});

export const addAdditionalCoursesFailed = (error) => ({
  type: ActionTypes.ADD_ADDITIONAL_COURSES_FAILED,
  payload: error,
});

export const addAdditionalCourses =
  (formData, setErrors, setTabsId) => (dispatch) => {
    dispatch(addAdditionalCoursesProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate-additional-course`, formData, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(addAdditionalCoursesSuccess(response.data));
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        setErrors({});
        setTabsId("5");
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(addAdditionalCoursesFailed(error.response.data));
          setErrors(error.response.data.errors);
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

export const getAdditionalCoursesByIdSuccess = (data) => ({
  type: ActionTypes.ADD_ADDITIONAL_COURSES_SUCCESS,
  payload: data,
});

export const getAdditionalCoursesByIdProcessing = () => ({
  type: ActionTypes.ADD_ADDITIONAL_COURSES_PROCESSING,
});

export const getAdditionalCoursesByIdFailed = (error) => ({
  type: ActionTypes.ADD_ADDITIONAL_COURSES_FAILED,
  payload: error,
});

export const getAdditionalCoursesById = (id) => (dispatch) => {
  dispatch(getAdditionalCoursesByIdProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate-additional-course/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) =>
      dispatch(getAdditionalCoursesByIdSuccess(response.data))
    )
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getAdditionalCoursesByIdFailed(error.response.data));
        // setErrors(error.response.data.errors);
      }
    });
};

export const updateCandidateJobHistorySuccess = (data) => ({
  type: ActionTypes.UPDATE_CANDIDATE_JOB_HISTORY_SUCCESS,
  payload: data,
});

export const updateCandidateJobHistoryProcessing = () => ({
  type: ActionTypes.UPDATE_CANDIDATE_JOB_HISTORY_PROCESSING,
});

export const updateCandidateJobHistoryFailed = (error) => ({
  type: ActionTypes.UPDATE_CANDIDATE_JOB_HISTORY_FAILED,
  payload: error,
});

export const updateCandidateJobHistory =
  (formData, setErrors) => (dispatch) => {
    dispatch(updateCandidateJobHistoryProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate-job-history/update`, formData, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(updateCandidateJobHistorySuccess(response));
        setErrors({});
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
          dispatch(updateCandidateJobHistoryFailed(error.response.data));
          setErrors(error.response.data.errors);
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

export const updateCandidateSkillsSuccess = (error) => ({
  type: ActionTypes.UPDATE_CANDIDATE_SKILLS_SUCCESS,
  payload: error,
});

export const updateCandidateSkillsProcessing = () => ({
  type: ActionTypes.UPDATE_CANDIDATE_SKILLS_PROCESSING,
});

export const updateCandidateSkillsFailed = (error) => ({
  type: ActionTypes.UPDATE_CANDIDATE_SKILLS_FAILED,
  payload: error,
});

export const updateCandidateSkills = (values) => (dispatch) => {
  dispatch(updateCandidateSkillsProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/open/candidate-skills/update`, values, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => {
      dispatch(updateCandidateSkillsSuccess(response));
      toast.success("Candidate Skills Update Successfully", {
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
      dispatch(updateCandidateSkillsFailed(error));
      toast.error("Candidate Skills Update Failed", {
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

export const updateComment = (id, comments, setOpen) => (dispatch) => {
  dispatch(updateCandidatePersonalInformationProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/timelines/updatecomment`,
      {
        id: id,
        comment: comments,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      }
    )
    .then((response) => {
      dispatch(updateCandidateActionIDSuccess(response));
      setOpen(false);
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
      dispatch(updateCandidateActionIDFailed(error));
      toast.error("Update Timeline Failed.", {
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

// export const getOffers = (id) => (dispatch) => {
//   const token = localStorage.getItem("token");
//   return axios
//     .get(`${baseURL}api/open/timelines/getoffers/` + id, {
//       headers: {
//         Authorization: `Bearer ${token}`,
//       },
//     })
//     .then();
// };

export const getCandidateTimelineSuccess = (error) => ({
  type: ActionTypes.GET_CANDIDATE_TIMELINE_SUCCESS,
  payload: error,
});

export const getCandidateTimelineProcessing = () => ({
  type: ActionTypes.GET_CANDIDATE_TIMELINE_PROCESSING,
});

export const getCandidateTimelineFailed = (error) => ({
  type: ActionTypes.GET_CANDIDATE_TIMELINE_FAILED,
  payload: error,
});

export const getCandidateTimeline = (id) => (dispatch) => {
  dispatch(getCandidateTimelineProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate_cv/timeline/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCandidateTimelineSuccess(response.data)))
    .catch((error) => dispatch(getCandidateTimelineFailed(error)));
};

export const getCandidateSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_SUCCESS,
  payload: data,
});

export const getCandidateProcessing = () => ({
  type: ActionTypes.GET_CANDIDATE_PROCESSING,
});

export const getCandidateFailed = (error) => ({
  type: ActionTypes.GET_CANDIDATE_FAILED,
  payload: error,
});

export const getAllCandidates =
  (
    pageNumber,
    searchTerm,
    gender,
    jobType,
    location,
    statusCountNumber,
    candidateListId,
    sorting
  ) =>
  (dispatch) => {
    dispatch(getCandidateProcessing());
    const token = localStorage.getItem("token");
    return axios
      .get(
        `${baseURL}api/open/candidate_cv?page=${pageNumber}&filter[search]=${searchTerm}&filter[gender]=${
          gender ? gender : ""
        }&filter[job-type]=${jobType ? jobType : ""}&filter[location]=${
          location ? location : ""
        }&filter[status]=${
          statusCountNumber ? statusCountNumber : ""
        }&filter[custom_list_id]=${
          candidateListId ? candidateListId : ""
        }&sort=${sorting !== null || undefined ? sorting : ""}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      )
      .then((response) => dispatch(getCandidateSuccess(response.data)))
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(getCandidateFailed(error.response.data));
        }
      });
  };

export const getCandidateAppliedJobsSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_APPLIED_JOBS_SUCCESS,
  payload: data,
});

export const getCandidateAppliedJobsProcessing = () => ({
  type: ActionTypes.GET_CANDIDATE_APPLIED_JOBS_PROCESSING,
});

export const getCandidateAppliedJobsFailed = (error) => ({
  type: ActionTypes.GET_CANDIDATE_APPLIED_JOBS_FAILED,
  payload: error,
});

export const getCandidateAppliedJobs = (id, actionId) => (dispatch) => {
  dispatch(getCandidateAppliedJobsProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/candidate_cv/jobs-applied`,
      {
        id: id,
        action_id: actionId,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => dispatch(getCandidateAppliedJobsSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getCandidateAppliedJobsFailed(error.response.data));
      }
    });
};

export const getCandidatePhaseJobsSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_PREVIOUS_PHASE_JOBS_SUCCESS,
  payload: data,
});

export const getCandidatePhaseJobsProcessing = () => ({
  type: ActionTypes.GET_CANDIDATE_PREVIOUS_PHASE_JOBS_PROCESSING,
});

export const getCandidatePhaseJobsFailed = (error) => ({
  type: ActionTypes.GET_CANDIDATE_PREVIOUS_PHASE_JOBS_FAILED,
  payload: error,
});

export const getCandidatePhaseJobs = (id, actionId) => (dispatch) => {
  dispatch(getCandidatePhaseJobsProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/candidate_cv/jobs-applied-previous-phase`,
      {
        id: id,
        action_id: actionId,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => dispatch(getCandidatePhaseJobsSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getCandidatePhaseJobsFailed(error.response.data));
      }
    });
};

export const getCandidateByIDSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_PROFILE_BY_ID_SUCCESS,
  payload: data,
});

export const getCandidateByIDProcessing = () => ({
  type: ActionTypes.GET_CANDIDATE_PROFILE_BY_ID_PROCESSING,
});

export const getCandidateByIDFailed = (error) => ({
  type: ActionTypes.GET_CANDIDATE_PROFILE_BY_ID_FAILED,
  payload: error,
});

export const getCandidateProfileById = (id) => (dispatch) => {
  dispatch(getCandidateByIDProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/candidate_cv/show/` + id,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => dispatch(getCandidateByIDSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getCandidateByIDFailed(error.response));
      }
    });
};

export const getAllCountData = () => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate_cv/getcountdata`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then();
};

export const getCandidateEducationSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_EDUCATION_SUCCESS,
  payload: data,
});

export const getCandidateEducationById = (id) => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate-education/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCandidateEducationSuccess(response.data)));
};

export const getCandidateJobHistorySuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_JOB_HISTORY_SUCCESS,
  payload: data,
});

export const getCandidateJobHistoryById = (id) => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate-job-history/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCandidateJobHistorySuccess(response.data)));
};

export const getCandidateSkillsByIdSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_SKILLS_SUCCESS,
  payload: data,
});

export const getCandidateSkillsById = (id) => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate-skills/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getCandidateSkillsByIdSuccess(response));
    });
};

export const deleteCandidateSuccess = (data) => ({
  type: ActionTypes.DELETE_CANDIDATE_SUCCESS,
  payload: data,
});

export const deleteCandidateProcessing = () => ({
  type: ActionTypes.DELETE_CANDIDATE_PROCESSING,
});

export const deleteCandidateFailed = (error) => ({
  type: ActionTypes.DELETE_CANDIDATE_FAILED,
  payload: error,
});

export const deleteCandidates =
  (id, setCandidateListIDs, fetchAllCandidates, fetchCandiatePhases) =>
  (dispatch) => {
    dispatch(deleteCandidateProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/candidate_cv/delete`,
        {
          id: id,
        },
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      )
      .then((response) => {
        dispatch(deleteCandidateSuccess(response.data));
        setCandidateListIDs([]);
        fetchAllCandidates();
        fetchCandiatePhases();
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
        dispatch(deleteCandidateFailed(error));
        toast.error("Candidate Delete Failed", {
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

export const deleteCandidateJobHistoryByIdSuccess = (data) => ({
  type: ActionTypes.DELETE_CANDIDATE_JOB_HISTORY_BY_ID_SUCCESS,
  payload: data,
});

export const deleteCandidateJobHistoryByIdProcessing = () => ({
  type: ActionTypes.DELETE_CANDIDATE_JOB_HISTORY_BY_ID_PROCESSING,
});

export const deleteCandidateJobHistoryByIdFailed = (error) => ({
  type: ActionTypes.DELETE_CANDIDATE_JOB_HISTORY_BY_ID_FAILED,
  payload: error,
});

export const deleteCandidateJobHistoryByIds = (id) => (dispatch) => {
  dispatch(deleteCandidateJobHistoryByIdProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/candidate-job-history/delete`,
      {
        id: id,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    )
    .then((response) => {
      dispatch(deleteCandidateJobHistoryByIdSuccess(response.data));
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
      dispatch(deleteCandidateJobHistoryByIdFailed(error.response));
      toast.error(error.response.data.message, {
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

export const deleteCandidateEducationByIdSuccess = (data) => ({
  type: ActionTypes.DELETE_CANDIDATE_EDUCATION_BY_ID_SUCCESS,
  payload: data,
});

export const deleteCandidateEducationByIdProcessing = () => ({
  type: ActionTypes.DELETE_CANDIDATE_EDUCATION_BY_ID_PROCESSING,
});

export const deleteCandidateEducationByIdFailed = (error) => ({
  type: ActionTypes.DELETE_CANDIDATE_EDUCATION_BY_ID_FAILED,
  payload: error,
});

export const deleteCandidateEducationByIds = (id) => (dispatch) => {
  dispatch(deleteCandidateEducationByIdProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/candidate-education/delete`,
      {
        id: id,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    )
    .then((response) => {
      dispatch(deleteCandidateEducationByIdSuccess(response.data));
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
      dispatch(deleteCandidateEducationByIdFailed(error.response));
      toast.error(error.response.data.message, {
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

export const getUploadFilesByIdSuccess = (data) => ({
  type: ActionTypes.GET_UPLOAD_FILES_BY_ID_SUCCESS,
  payload: data,
});

export const getUploadFilesByIdProcessing = () => ({
  type: ActionTypes.GET_UPLOAD_FILES_BY_ID_PROCESSING,
});

export const getUploadFilesByIdFailed = (error) => ({
  type: ActionTypes.GET_UPLOAD_FILES_BY_ID_FAILED,
  payload: error,
});

export const getUploadFilesById = (id) => (dispatch) => {
  dispatch(getUploadFilesByIdProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate-files/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "multipart/form-data",
      },
    })
    .then((response) => dispatch(getUploadFilesByIdSuccess(response)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getUploadFilesByIdFailed(error.response.data));
      }
    });
};

export const deleteUploadFilesByIdSuccess = (data) => ({
  type: ActionTypes.DELETE_UPLOAD_FILES_SUCCESS,
  payload: data,
});

export const deleteUploadFilesByIdProcessing = () => ({
  type: ActionTypes.DELETE_UPLOAD_FILES_PROCESSING,
});

export const deleteUploadFilesByIdFailed = (error) => ({
  type: ActionTypes.DELETE_UPLOAD_FILES_FAILED,
  payload: error,
});

export const deleteUploadFilesById =
  (id, candidateCvId, setTriggerUpdate) => (dispatch) => {
    dispatch(deleteUploadFilesByIdProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/candidate-files/delete`,
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
        dispatch(deleteUploadFilesByIdSuccess(response.data));
        // dispatch(getUploadFilesById(candidateCvId));
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        setTriggerUpdate({});
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(deleteUploadFilesByIdFailed(error.response.data));
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

export const uploadFileSuccess = (data) => ({
  type: ActionTypes.UPLOAD_FILES_SUCCESS,
  payload: data,
});

export const uploadFileProcessing = () => ({
  type: ActionTypes.UPLOAD_FILES_PROCESSING,
});

export const uploadFileFailed = (error) => ({
  type: ActionTypes.UPLOAD_FILES_FAILED,
  payload: error,
});

export const uploadFiles =
  (
    oldFormData,
    setUploadCandidateFiles,
    candidateCvId,
    setModal,
    setErrors,
    fetchCandidateFilesByID,
    setTabsId
  ) =>
  (dispatch) => {
    dispatch(uploadFileProcessing());

    const token = localStorage.getItem("token");
    const formData = new FormData();
    formData.append("candidatecv_id", oldFormData.candidatecv_id);
    formData.append("files", oldFormData.files);
    for (const file of oldFormData["files"]) {
      formData.append("files[]", file);
    }

    return axios
      .post(`${baseURL}api/open/candidate-files`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(uploadFileSuccess(response.data));
        fetchCandidateFilesByID();
        setTabsId("7");
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        setUploadCandidateFiles([]);
        setModal(false);
        setErrors({});
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(uploadFileFailed(error.response.data));
          setErrors(error.response.data.errors);
          setUploadCandidateFiles([]);
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

export const getCandidateEmailDataSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATE_DATA_SUCCESS,
  payload: data,
});

export const getCandidateEmailDataProcessing = () => ({
  type: ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATE_DATA_PROCESSING,
});

export const getCandidateEmailDataFailed = (error) => ({
  type: ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATE_DATA_FAILED,
  payload: error,
});

export const getCandidateEmailData = (id, templateID) => (dispatch) => {
  dispatch(getCandidateEmailDataProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/mail/templates/${templateID}/candidate/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCandidateEmailDataSuccess(response)))
    .catch((error) => {
      dispatch(getCandidateEmailDataFailed(error.response.data));
      if (error.response !== undefined) {
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

export const candidateCreateCustomListSuccess = (data) => ({
  type: ActionTypes.CREATE_CUSTOM_LIST_SUCCESS,
  payload: data,
});

export const candidateCreateCustomListProcessing = () => ({
  type: ActionTypes.CREATE_CUSTOM_LIST_PROCESSING,
});

export const candidateCreateCustomListFailed = (error) => ({
  type: ActionTypes.CREATE_CUSTOM_LIST_FAILED,
  payload: error,
});

export const candidateCreateCustomLists =
  (values, setCustomListModal, setCustomList, setCandidateListIDs, setErrors) =>
  (dispatch) => {
    dispatch(candidateCreateCustomListProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/custom_candidate_list`, values, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(candidateCreateCustomListSuccess(response.data));
        setCustomList(false);
        dispatch(getCandidateCustomLists());
        setCustomListModal(false);
        setCandidateListIDs([]);
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
          dispatch(candidateCreateCustomListFailed(error.response));
          setErrors(error.response.data.errors);
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

export const candidateCreateCustomListMultipleSuccess = (data) => ({
  type: ActionTypes.CREATE_CUSTOM_LIST_MULTIPLE_SUCCESS,
  payload: data,
});

export const candidateCreateCustomListMultipleProcessing = () => ({
  type: ActionTypes.CREATE_CUSTOM_LIST_MULTIPLE_PROCESSING,
});

export const candidateCreateCustomListMultipleFailed = (error) => ({
  type: ActionTypes.CREATE_CUSTOM_LIST_MULTIPLE_FAILED,
  payload: error,
});

export const candidateCreateCustomListMultiple =
  (
    formData,
    setCandidateListIDs,
    setCustomCandidateListModal,
    setErrors,
    fetchCandidateCustomList
  ) =>
  (dispatch) => {
    dispatch(candidateCreateCustomListMultipleProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/custom_candidate_list/add-multiple-candidates-multiple-lists`,
        formData,
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      )
      .then((response) => {
        dispatch(candidateCreateCustomListMultipleSuccess(response.data));
        setCandidateListIDs([]);
        setCustomCandidateListModal(false);
        fetchCandidateCustomList();
        setErrors({});
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
          dispatch(
            candidateCreateCustomListMultipleFailed(error.response.data)
          );
          setErrors(error.response.data.errors);
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

export const candidateRemoveCustomListMultipleSuccess = (data) => ({
  type: ActionTypes.REMOVE_CUSTOM_LIST_MULTIPLE_SUCCESS,
  payload: data,
});

export const candidateRemoveCustomListMultipleProcessing = () => ({
  type: ActionTypes.REMOVE_CUSTOM_LIST_MULTIPLE_PROCESSING,
});

export const candidateRemoveCustomListMultipleFailed = (error) => ({
  type: ActionTypes.REMOVE_CUSTOM_LIST_MULTIPLE_FAILED,
  payload: error,
});

export const candidateRemoveCustomListMultiple =
  (
    formData,
    setCandidateListIDs,
    setCustomCandidateListModal,
    setErrors,
    fetchAllCandidates
  ) =>
  (dispatch) => {
    dispatch(candidateRemoveCustomListMultipleProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/custom_candidate_list/remove-multiple-candidates-multiple-lists`,
        formData,
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      )
      .then((response) => {
        dispatch(candidateRemoveCustomListMultipleSuccess(response.data));
        setCandidateListIDs([]);
        setCustomCandidateListModal(false);
        fetchAllCandidates();
        setErrors({});
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
          dispatch(
            candidateRemoveCustomListMultipleFailed(error.response.data)
          );
          setErrors(error.response.data.errors);
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

export const candidateDeleteSingleCustomListSuccess = (data) => ({
  type: ActionTypes.DELETE_SINGLE_CUSTOM_LIST_SUCCESS,
  payload: data,
});

export const candidateDeleteSingleCustomListProcessing = () => ({
  type: ActionTypes.DELETE_SINGLE_CUSTOM_LIST_PROCESSING,
});

export const candidateDeleteSingleCustomListFailed = (error) => ({
  type: ActionTypes.DELETE_SINGLE_CUSTOM_LIST_FAILED,
  payload: error,
});

export const candidateDeleteSingleCustomList = (id) => (dispatch) => {
  dispatch(candidateDeleteSingleCustomListProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/custom_candidate_list/delete`,
      {
        candidate_id: id,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    )
    .then((response) => {
      dispatch(candidateDeleteSingleCustomListSuccess(response.data));
      dispatch(getCandidateCustomLists());
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
      dispatch(candidateDeleteSingleCustomListFailed(error));
      toast.error(error.response.data.message, {
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

export const candidateUpdateCustomListNameSuccess = (data) => ({
  type: ActionTypes.UPDATE_CUSTOM_LIST_NAME_SUCCESS,
  payload: data,
});

export const candidateUpdateCustomListNameProcessing = () => ({
  type: ActionTypes.UPDATE_CUSTOM_LIST_NAME_PROCESSING,
});

export const candidateUpdateCustomListNameFailed = (error) => ({
  type: ActionTypes.UPDATE_CUSTOM_LIST_NAME_FAILED,
  payload: error,
});

export const candidateUpdateCustomListName =
  (values, setEditCustomListNameModal) => (dispatch) => {
    dispatch(candidateUpdateCustomListNameProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/custom_candidate_list_name/update`, values, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(candidateUpdateCustomListNameSuccess(response.data));
        dispatch(getCandidateCustomLists());
        setEditCustomListNameModal(false);
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
        dispatch(candidateUpdateCustomListNameFailed(error));
        toast.error(error.response.data.message, {
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

export const candidateUpdateCustomListSuccess = (data) => ({
  type: ActionTypes.UPDATE_CUSTOM_LIST_SUCCESS,
  payload: data,
});

export const candidateUpdateCustomListProcessing = () => ({
  type: ActionTypes.UPDATE_CUSTOM_LIST_PROCESSING,
});

export const candidateUpdateCustomListFailed = (error) => ({
  type: ActionTypes.UPDATE_CUSTOM_LIST_FAILED,
  payload: error,
});

export const candidateUpdateCustomList =
  (values, setUpdateCustomList) => (dispatch) => {
    dispatch(candidateUpdateCustomListProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/custom_candidate_list/update`, values, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(candidateUpdateCustomListSuccess(response.data));
        dispatch(getCandidateCustomLists());
        setUpdateCustomList(false);
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
        dispatch(candidateUpdateCustomListFailed(error));
        toast.error(error.response.data.message, {
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

export const candidateDeleteCustomListSuccess = (data) => ({
  type: ActionTypes.DELETE_CUSTOM_LIST_NAME_SUCCESS,
  payload: data,
});

export const candidateDeleteCustomListProcessing = () => ({
  type: ActionTypes.DELETE_CUSTOM_LIST_NAME_PROCESSING,
});

export const candidateDeleteCustomListFailed = (error) => ({
  type: ActionTypes.DELETE_CUSTOM_LIST_NAME_FAILED,
  payload: error,
});

export const candidateDeleteCustomList =
  (id, setCandidateListId, setTriggerUpdate) => (dispatch) => {
    dispatch(candidateDeleteCustomListProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/custom_candidate_list/delete`,
        {
          list_name_id: id,
        },
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      )
      .then((response) => {
        dispatch(candidateDeleteCustomListSuccess(response.data));
        setCandidateListId("");
        setTriggerUpdate({});
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
        dispatch(candidateDeleteCustomListFailed(error));
        toast.error(error.response.data.message, {
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

export const getCandidateCustomListSuccess = (data) => ({
  type: ActionTypes.GET_CUSTOM_LIST_SUCCESS,
  payload: data,
});

export const getCandidateCustomListProcessing = () => ({
  type: ActionTypes.GET_CUSTOM_LIST_PROCESSING,
});

export const getCandidateCustomListFailed = (error) => ({
  type: ActionTypes.GET_CUSTOM_LIST_FAILED,
  payload: error,
});

export const getCandidateCustomLists = () => (dispatch) => {
  dispatch(getCandidateCustomListProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/custom_candidate_list`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => {
      dispatch(getCandidateCustomListSuccess(response.data));
    })
    .catch((error) => {
      dispatch(getCandidateCustomListFailed(error));
    });
};

export const fetchBestMatchingJobsSuccess = (data) => ({
  type: ActionTypes.FETCH_BEST_MATCH_JOBS_SUCCESS,
  payload: data,
});

export const fetchBestMatchingJobsProcessing = () => ({
  type: ActionTypes.FETCH_BEST_MATCH_JOBS_PROCESSING,
});

export const fetchBestMatchingJobsFailed = (error) => ({
  type: ActionTypes.FETCH_BEST_MATCH_JOBS_FAILED,
  payload: error,
});

export const fetchBestMatchingJobs = (id) => (dispatch) => {
  dispatch(fetchBestMatchingJobsProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate_cv/get-best-match/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => dispatch(fetchBestMatchingJobsSuccess(response.data)))
    .catch((error) => dispatch(fetchBestMatchingJobsFailed(error)));
};

export const getCandidateEmailLinkSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATES_LINK_SUCCESS,
  payload: data,
});

export const getCandidateEmailLinkProcessing = () => ({
  type: ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATES_LINK_PROCESSING,
});

export const getCandidateEmailLinkFailed = (error) => ({
  type: ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATES_LINK_FAILED,
  payload: error,
});

export const getCandidateEmailLinkData =
  (id, templateID, setTemplateData, setFormValues, formValues, setBody) =>
  (dispatch) => {
    dispatch(getCandidateEmailLinkProcessing());

    const token = localStorage.getItem("token");
    return axios
      .get(
        `${baseURL}api/mail/templates/${templateID}/candidate-profile/${id}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      )
      .then((response) => {
        dispatch(getCandidateEmailLinkSuccess(response.data));
        setTemplateData(response.data.template);
        setFormValues({
          ...formValues,
          to: response.data.template.to,
          subject: response.data.template.title,
        });
        setBody(response.data.template.body);
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(getCandidateEmailLinkFailed(error.response.data));
        }
      });
  };
