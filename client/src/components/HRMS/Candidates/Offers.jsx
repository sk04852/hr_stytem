import React, { useEffect, useState } from "react";
import "./candidate.css";
import { useDispatch, useSelector } from "react-redux";
import {
  fetchBestMatchingJobs,
  getAllCandidates,
  getCandidateAppliedJobs,
  // getCandidateProfileById,
} from "../../../redux/actions/candidatesAction";
import { DataLoader } from "../../constants/loaders";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Modal,
  ModalBody,
  ModalHeader,
  Table,
} from "reactstrap";
import { useHistory, useLocation, useParams } from "react-router-dom";
// import { getAllActiveJobs } from "../../../redux/actions/jobsAction";
import { getUserProfile } from "../../../redux/actions/usersAction";
import { NoListFound } from "../../constants/noRecordFound";
import BestJobMatch from "./BestJobMatch";
import { useTranslation } from "react-i18next";

const Offers = ({ candidateItemById, triggerUpdate, setTriggerUpdate }) => {
  const { id } = useParams();

  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  let candidateJobOfferState = location.pathname;

  const [bestMatchData, setBestMatchData] = useState([]);
  const [jobData, setJobData] = useState("");
  const [jobModal, setJobModal] = useState(false);
  const [candidateDataPass, setCandidateDataPass] = useState(null);
  const [userdata, setUserData] = useState("");
  const [userPermission, setUserPermission] = useState([]);
  const [allCandidates, setAllCandidates] = useState([]);
  const [candidateById, setCandidateById] = useState([]);
  const [fetchAppliedJobsUpdate, setFetchAppliedJobsUpdate] = useState(null);

  const toggleJobModal = (temp) => {
    if (candidateDataPass == null) {
      setCandidateDataPass(temp);
    } else {
      setCandidateDataPass(null);
    }
    setJobModal(!jobModal);
  };

  useEffect(() => {
    const fetchBestMatchingJobsData = async () => {
      try {
        const response = await dispatch(fetchBestMatchingJobs(id));
        let matchingData = response.payload.data.hits;
        setBestMatchData(matchingData);
      } catch (error) {
        throw error;
      }
    };
    fetchBestMatchingJobsData();
  }, [triggerUpdate]);

  const candidateSelector = useSelector((state) => state.candidates);
  const settingsSelector = useSelector((state) => state.settings);

  // const fetchAllJobs = async () => {
  //   try {
  //     const response = await dispatch(getAllActiveJobs());
  //     if (response) {
  //       setJobData(response.payload.data);
  //     }
  //   } catch (error) {
  //     throw error;
  //   }
  // };

  const fetchUserProfile = async () => {
    try {
      const response = await dispatch(getUserProfile());
      setUserData(response.payload.data);
    } catch (error) {
      throw error;
    }
  };

  const fetchAllCandidates = async () => {
    try {
      const response = await dispatch(
        getAllCandidates(1, "", "", "", "", "", "", "")
      );

      if (response.payload.data.length === 0) {
        setAllCandidates([]);
      } else {
        setAllCandidates(response.payload.data.data);
      }
    } catch (error) {
      throw error;
    }
  };

  // const fetchCandidateProfile = async () => {
  //   try {
  //     const response = await dispatch(getCandidateProfileById(id));
  //     console.log(response);
  //   } catch (error) {
  //     throw error;
  //   }
  // };

  const fetchCandidateAppliedJobs = async () => {
    try {
      const response = await dispatch(getCandidateAppliedJobs(id));
      setCandidateById(response.payload.data);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    // fetchAllJobs();
    fetchUserProfile();
    // fetchCandidateProfile();
  }, []);

  useEffect(() => {
    fetchCandidateAppliedJobs();
  }, [fetchAppliedJobsUpdate]);

  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );

  useEffect(() => {
    if (getUserProfileSelector) {
      setUserPermission(getUserProfileSelector.permissions);
    }
  }, [getUserProfileSelector]);

  const renderJobUpdateModal = () => {
    return (
      <Modal isOpen={jobModal} toggle={toggleJobModal}>
        <ModalHeader
          toggle={toggleJobModal}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "15px"
            }`,
          }}
        >
          {t("candidates.candidateApplyForJob")}
        </ModalHeader>
        <ModalBody>
          <BestJobMatch
            bestMatchData={bestMatchData}
            candidateId={id}
            setTriggerUpdate={setTriggerUpdate}
            handleFetchBestMatchingJobs={() => {}}
            fetchAllCandidates={fetchAllCandidates}
            fetchCandidateAppliedJobs={fetchCandidateAppliedJobs}
            offerPage={true}
            setFetchAppliedJobsUpdate={setFetchAppliedJobsUpdate}
          />
        </ModalBody>
      </Modal>
    );
  };

  return (
    <>
      {renderJobUpdateModal()}
      <Card>
        <CardHeader className="float-right">
          <h1>{t("candidates.jobs")}</h1>
        </CardHeader>
        <div className="header-action w-100 text-right pr-3">
          {/* {candidateById ? (
            <>
              {candidateById[0]?.action_id > 1 &&
              candidateById[0]?.action_id < 8 ? null : ( */}
          <Button
            type="button"
            className="btn btn-primary"
            data-toggle="modal"
            onClick={() => setJobModal(true)}
            style={{
              fontSize: `${
                settingsSelector.FontSize === "Large"
                  ? "large"
                  : settingsSelector.FontSize === "Extra Large"
                  ? "x-large"
                  : "14px"
              }`,
            }}
          >
            <i className="fe fe-plus mr-2" />
            {t("candidates.candidateApplyForJob")}
          </Button>
          {/* )}
            </>
          ) : null} */}
        </div>
        <CardBody>
          <div className="table-responsive">
            <Table
              id={"candidate-table"}
              className="table-hover table-striped table-vcenter text-nowrap mb-0"
            >
              <thead>
                <tr>
                  <th
                    style={{
                      paddingRight: "50px",
                      fontSize: `${
                        settingsSelector.FontSize === "Large"
                          ? "large"
                          : settingsSelector.FontSize === "Extra Large"
                          ? "x-large"
                          : "14px"
                      }`,
                    }}
                  >
                    {t("candidates.jobs")}
                  </th>
                  <th
                    style={{
                      paddingRight: "50px",
                      fontSize: `${
                        settingsSelector.FontSize === "Large"
                          ? "large"
                          : settingsSelector.FontSize === "Extra Large"
                          ? "x-large"
                          : "14px"
                      }`,
                    }}
                  >
                    {t("candidates.candidatePhase")}
                  </th>
                </tr>
              </thead>
              <tbody>
                {!candidateSelector.isLoading ? (
                  candidateById ? (
                    candidateById.length > 0 ? (
                      candidateById.map((jobOfferName, index) => (
                        <tr key={index}>
                          <td
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${jobOfferName.job_pr_id}`,
                                state: {
                                  userPermission,
                                  candidateJobOfferState,
                                },
                              })
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              cursor: "pointer",
                            }}
                          >
                            {jobOfferName.offer_name}
                          </td>
                          <td
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${jobOfferName.job_pr_id}`,
                                state: {
                                  userPermission,
                                  candidateJobOfferState,
                                },
                              })
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              cursor: "pointer",
                            }}
                          >
                            {jobOfferName.action_name}
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td>
                          <NoListFound />
                        </td>
                      </tr>
                    )
                  ) : (
                    ""
                  )
                ) : (
                  <tr>
                    <td>
                      <DataLoader />
                    </td>
                  </tr>
                )}
              </tbody>
            </Table>
          </div>
        </CardBody>
      </Card>
    </>
  );
};

export default Offers;
