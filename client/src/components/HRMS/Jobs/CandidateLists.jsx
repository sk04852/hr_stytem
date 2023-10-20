import React, { useEffect, useState } from "react";
import {
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Progress,
} from "reactstrap";
import { DataLoader } from "../../constants/loaders";
import { fetchBestMatchingCandidates } from "../../../redux/actions/jobsAction";
import { useDispatch, useSelector } from "react-redux";
import { useHistory, useLocation, useParams } from "react-router-dom";
import { NoListFound } from "../../constants/noRecordFound";
import { UndefinedError } from "../../constants/errors";
import { useTranslation } from "react-i18next";

const CandidateLists = () => {
  const { id } = useParams();
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  let jobsState = location.pathname;

  const [bestMatchCandidates, setBestMatchCandidates] = useState([]);
  const [userPermission, setUserPermission] = useState([]);

  useEffect(() => {
    const fetchBestCandidatesMatchData = async () => {
      try {
        const response = await dispatch(fetchBestMatchingCandidates(id));
        let matchingCandidateData = response.payload.data.hits;
        setBestMatchCandidates(matchingCandidateData);
      } catch (error) {
        throw error;
      }
    };
    fetchBestCandidatesMatchData();
  }, []);

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
              {/*Kandidaadid*/}
              {t("jobs.suitableCandidates")}
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
                  {/*  Kandideerimise kuupäev*/}
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
                    {t("jobs.suitability")}
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
                    {t("actions")}
                  </th>
                  <th />
                </tr>
              </thead>
              <tbody>
                {!candidateSelector.isLoading ? (
                  bestMatchCandidates === undefined ? (
                    <UndefinedError />
                  ) : bestMatchCandidates.length > 0 ? (
                    bestMatchCandidates.map((item, index) => (
                      <tr key={index}>
                        <td
                          onClick={() =>
                            history.push({
                              pathname: `/hr-candidate/view/${item.document.id}/1`,
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
                          {item && item.document.content.first_name}{" "}
                          {item && item.document.content.last_name}
                        </td>
                        <td
                          onClick={() =>
                            history.push({
                              pathname: `/hr-candidate/view/${item.document.id}/1`,
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
                          {item.document.content.email}
                        </td>
                        <td
                          onClick={() =>
                            history.push({
                              pathname: `/hr-candidate/view/${item.document.id}/1`,
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
                          {item.document.content.action}
                        </td>
                        <td
                          onClick={() =>
                            history.push({
                              pathname: `/hr-candidate/view/${item.document.id}/1`,
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
                          {`${
                            item.score > 100 ? "100" : item.score.toFixed(2)
                          }%`}
                          <Progress
                            value={item.score.toFixed(2)}
                            color={
                              item.score.toFixed(2) >= 0 &&
                              item.score.toFixed(2) <= 50
                                ? "danger"
                                : item.score.toFixed(2) >= 51 &&
                                  item.score.toFixed(2) <= 70
                                ? "warning"
                                : "success"
                            }
                            style={{ width: "200px", height: "0.5rem" }}
                            className={"mt-1"}
                          />
                        </td>
                        <td>
                          <button
                            type="button"
                            className="btn btn-icon btn-sm"
                            title="View"
                            onClick={() =>
                              history.push({
                                pathname: `/hr-candidate/view/${item.document.id}/1`,
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

export default CandidateLists;
