import React, { useEffect, useState } from "react";
import { Card, CardBody, CardHeader, CardTitle, Col } from "reactstrap";
import { DataLoader } from "../../constants/loaders";
import { getJobsApplicants } from "../../../redux/actions/jobsAction";
import { useDispatch, useSelector } from "react-redux";
import { useHistory, useLocation, useParams } from "react-router-dom";
import { NoListFound } from "../../constants/noRecordFound";
import { UndefinedError } from "../../constants/errors";
import { useTranslation } from "react-i18next";

const JobApplicants = ({ triggerUpdate }) => {
  const { id } = useParams();
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  const [allJobApplicants, setAllJobApplicants] = useState([]);
  const [userPermission, setUserPermission] = useState([]);

  let jobsState = location.pathname;

  const fetchJobApplicants = async () => {
    try {
      const response = await dispatch(getJobsApplicants(id));
      let jobApplicantsResponse = response.payload.candidates.data;
      setAllJobApplicants(jobApplicantsResponse);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchJobApplicants();
  }, [triggerUpdate]);

  const candidateSelector = useSelector((state) => state.candidates);
  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (getUserProfileSelector) {
      setUserPermission(getUserProfileSelector.permissions);
    }
  }, [getUserProfileSelector]);

  return (
    <Col lg={12} md={12} sm={12}>
      <Card>
        <CardHeader>
          <CardTitle>
            <h5
              style={{
                fontSize: `${
                  settingsSelector.FontSize === "Large"
                    ? "1.75rem"
                    : settingsSelector.FontSize === "Extra Large"
                    ? "2.25rem"
                    : "1.25rem"
                }`,
              }}
            >
              {/* Candidates */}
              {t("jobs.jobApplicants")}
            </h5>
          </CardTitle>
        </CardHeader>
        <CardBody>
          <div className="table-responsive">
            <table className="table table-hover table-striped table-vcenter mb-0">
              <thead>
                <tr>
                  <th
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
                    {t("candidates.candidateName")}
                  </th>
                  <th
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
                    {t("candidates.bookmarks.personalInformation.email")}
                  </th>
                  <th
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
                    {t("candidates.candidatePhase")}
                  </th>
                  <th
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
                    {t("jobs.applicationDate")}
                  </th>
                  {/*<th*/}
                  {/*  style={{*/}
                  {/*    fontSize: `${*/}
                  {/*      settingsSelector.FontSize === "Large"*/}
                  {/*        ? "large"*/}
                  {/*        : settingsSelector.FontSize === "Extra Large"*/}
                  {/*        ? "x-large"*/}
                  {/*        : "14px"*/}
                  {/*    }`,*/}
                  {/*  }}*/}
                  {/*>*/}
                  {/*  Sobivus*/}
                  {/*</th>*/}
                  <th
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
                    {t("actions")}
                  </th>
                  <th />
                </tr>
              </thead>
              <tbody>
                {!candidateSelector.isLoading ? (
                  allJobApplicants === undefined ? (
                    <UndefinedError />
                  ) : allJobApplicants.length > 0 ? (
                    allJobApplicants.map((item, index) => (
                      <tr key={index}>
                        <td
                          onClick={() =>
                            history.push({
                              pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                              state: { userPermission, jobsState },
                            })
                          }
                          style={{
                            cursor: "pointer",
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          {item && item.first_name} {item && item.last_name}
                        </td>
                        <td
                          onClick={() =>
                            history.push({
                              pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                              state: { userPermission, jobsState },
                            })
                          }
                          style={{
                            cursor: "pointer",
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          {item.email}
                        </td>
                        <td
                          onClick={() =>
                            history.push({
                              pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                              state: { userPermission, jobsState },
                            })
                          }
                          style={{
                            cursor: "pointer",
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          {item.action_name}
                        </td>
                        <td
                          onClick={() =>
                            history.push({
                              pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                              state: { userPermission, jobsState },
                            })
                          }
                          style={{
                            cursor: "pointer",
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          {item.created_at.substring(0, 10)}
                        </td>
                        <td>
                          <button
                            type="button"
                            className="btn btn-icon btn-sm"
                            title="View"
                            onClick={() =>
                              history.push({
                                pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                                state: { userPermission, jobsState },
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
                            }}
                          >
                            <i className="fa fa-eye" />
                          </button>
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
                  <tr>
                    <td>
                      <DataLoader />
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </CardBody>
      </Card>
    </Col>
  );
};

export default JobApplicants;
