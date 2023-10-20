import React, { useState } from "react";
import {
  Container,
  Row,
  Card,
  CardHeader,
  CardBody,
  CardTitle,
} from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import { NoRecordFound } from "../../constants/noRecordFound";
import { DataLoader } from "../../constants/loaders";
import { useHistory, useLocation, useParams } from "react-router-dom";
import { candidatesApplyToJobForJobs } from "../../../redux/actions/candidatesAction";
import Paginator from "react-hooks-paginator";

const FilteredCandidates = ({
  filteredCandidates,
  totalRecords,
  setOffset,
  pageNumber,
  setPageNumber,
  setApplicantsSorting,
  fetchJobsApplicants,
  setTriggerUpdate,
}) => {
  const { id } = useParams();
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  let jobLocation = location.pathname;

  const { t } = useTranslation();

  const [sortingOrder, setSortingOrder] = useState("asc");

  const settingsSelector = useSelector((state) => state.settings);
  const candidateSelector = useSelector((state) => state.candidates);

  const handleSorting = (e) => {
    if (sortingOrder === "asc") {
      setSortingOrder("dsc");
      setApplicantsSorting("-" + e);
    }
    if (sortingOrder === "dsc") {
      setSortingOrder("asc");
      setApplicantsSorting(e);
    }
  };

  const candidateApplyToJob = (
    action_id,
    candidatecv_id,
    job_id,
    template_key
  ) => {
    dispatch(
      candidatesApplyToJobForJobs(
        action_id,
        candidatecv_id,
        job_id,
        template_key,
        history,
        setTriggerUpdate,
        fetchJobsApplicants,
        jobLocation
      )
    );
  };

  return (
    <Container fluid>
      <Row>
        <Card>
          <CardHeader>
            <CardTitle className={"w-100"}>
              <Row className={"w-100 d-flex justify-content-between"}>
                <h5
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "x-large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "xx-large"
                        : "1.25rem"
                    }`,
                  }}
                >
                  {t("candidates.candidateList")}
                </h5>
                <form>
                  <div className="input-group">
                    <input
                      type="text"
                      className="form-control form-control-sm"
                      placeholder="Search something..."
                      name="s"
                      // onChange={optimizedSearchFn}
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    />
                  </div>
                </form>
              </Row>
            </CardTitle>
          </CardHeader>
          <CardBody>
            <div className="table-responsive">
              <table
                id={"candidate-table"}
                className="table table-hover table-striped table-vcenter text-nowrap mb-0 table-sortable"
              >
                <thead>
                  <tr>
                    <th
                      className="no-sort"
                      style={{
                        // paddingRight: "50px",
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    >
                      {t("candidates.candidatePicture")}
                    </th>
                    <th
                      onClick={() => handleSorting("first_name")}
                      className={sortingOrder === "asc" ? "asc" : "dsc"}
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
                      {t("candidates.candidateName")}
                    </th>

                    <th
                      className="no-sort"
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
                      // onClick={() => handleSorting("phone")}
                      // className={sortingOrder === "asc" ? "asc" : "dsc"}
                    >
                      {t("candidates.candidatePhone")}
                    </th>
                    <th
                      className="no-sort"
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
                      {t("candidates.candidateGender")}
                    </th>
                    <th
                      onClick={() => handleSorting("action_name")}
                      className={sortingOrder === "asc" ? "asc" : "dsc"}
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
                    <th
                      onClick={() => handleSorting("location")}
                      className={sortingOrder === "asc" ? "asc" : "dsc"}
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
                      {t("candidates.candidateLocation")}
                    </th>
                    <th
                      className="no-sort"
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
                      {t("candidates.candidateJobType")}
                    </th>
                    <th
                      className="no-sort"
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
                  </tr>
                </thead>
                <tbody>
                  {!candidateSelector.isLoading ? (
                    <>
                      {filteredCandidates && filteredCandidates.length > 0 ? (
                        filteredCandidates.map((c, index) => (
                          <tr key={index}>
                            <td
                              className="d-flex"
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${c.candidatecv_id}/1`,
                                  // state: userPermission,
                                })
                              }
                              style={{ cursor: "pointer" }}
                            >
                              <img
                                src={c && c.photo}
                                alt={"image"}
                                className="avatar avatar-blue"
                                data-toggle="tooltip"
                                data-original-title="Avatar Name"
                              />
                            </td>
                            <td
                              style={{
                                paddingRight: "50px",
                                cursor: "pointer",
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "1rem"
                                }`,
                              }}
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${c.candidatecv_id}/1`,
                                  // state: userPermission,
                                })
                              }
                            >
                              <h6
                                className="mb-0"
                                style={{
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "1rem"
                                  }`,
                                }}
                              >
                                {" "}
                                {c && c.first_name} {c && c.last_name}
                              </h6>
                              <span>{c && c.email}</span>
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${c.candidatecv_id}/1`,
                                  // state: userPermission,
                                })
                              }
                              style={{
                                cursor: "pointer",
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            >
                              {c && c.phone}
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${c.candidatecv_id}/1`,
                                  // state: userPermission,
                                })
                              }
                              style={{
                                cursor: "pointer",
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            >
                              {c && c.gender}
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${c.candidatecv_id}/1`,
                                  // state: userPermission,
                                })
                              }
                              style={{
                                cursor: "pointer",
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            >
                              <span
                                className="d-inline-block text-truncate"
                                style={{ maxWidth: "200px" }}
                              >
                                {c && c.action_name}
                                {/*{c && c.action_name === null*/}
                                {/*  ? ""*/}
                                {/*  : c.action_name === "General Candidates List"*/}
                                {/*  ? "Total Candidates List"*/}
                                {/*  : c.action_name}*/}
                              </span>
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${c.candidatecv_id}/1`,
                                  // state: userPermission,
                                })
                              }
                              style={{
                                cursor: "pointer",
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            >
                              {c && c.location}
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${c.candidatecv_id}/1`,
                                  // state: userPermission,
                                })
                              }
                              style={{
                                cursor: "pointer",
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            >
                              {c.job_type
                                ? c.job_type
                                    .split(",")
                                    .map((jobType, index) => (
                                      <div key={index}>{jobType}</div>
                                    ))
                                : ""}
                            </td>
                            <td>
                              <button
                                type="button"
                                className="btn btn-icon btn-sm"
                                title="View"
                                onClick={() =>
                                  history.push({
                                    pathname: `/hr-candidate/view/${c.candidatecv_id}/1`,
                                    // state: userPermission,
                                  })
                                }
                                style={{
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "14px"
                                  }`,
                                }}
                              >
                                <i className="fa fa-eye" />
                              </button>
                              <div className="dropdown">
                                <i
                                  className="fa fa-angle-down dropdown-toggle pt-2 pl-2"
                                  id="dropdownMenuButton"
                                  data-toggle="dropdown"
                                  aria-haspopup="true"
                                  aria-expanded="false"
                                  data-spy="scroll"
                                  data-boundary="window"
                                />
                                <div
                                  className="dropdown-menu"
                                  aria-labelledby="dropdownMenuButton"
                                >
                                  <button
                                    type="button"
                                    className="close pr-3"
                                    data-dismiss="modal"
                                    aria-label="Close"
                                  />
                                  {c.action_id < 3 ? (
                                    <a
                                      className="dropdown-item"
                                      href="#"
                                      onClick={(event) =>
                                        candidateApplyToJob(
                                          3,
                                          c.candidatecv_id,
                                          id
                                        )
                                      }
                                    >
                                      {/*Shortlisted*/}
                                      {t("candidates.sendInterviewInformation")}
                                    </a>
                                  ) : (
                                    <a
                                      className="dropdown-item color"
                                      style={{ pointerEvents: "none" }}
                                    >
                                      {/*Shortlisted*/}
                                      {t("candidates.sendInterviewInformation")}
                                    </a>
                                  )}
                                  {c.action_id < 4 ? (
                                    <a
                                      className="dropdown-item"
                                      href="#"
                                      onClick={(event) =>
                                        candidateApplyToJob(
                                          4,
                                          c.candidatecv_id,
                                          id,
                                          "leping"
                                        )
                                      }
                                    >
                                      {/*1st interview*/}
                                      {t("candidates.1stInterview")}
                                    </a>
                                  ) : (
                                    <a
                                      className="dropdown-item"
                                      style={{ pointerEvents: "none" }}
                                    >
                                      {/*1st interview*/}
                                      {t("candidates.1stInterview")}
                                    </a>
                                  )}
                                  {c.action_id < 5 ? (
                                    <a
                                      className="dropdown-item"
                                      href="#"
                                      onClick={(event) =>
                                        candidateApplyToJob(
                                          5,
                                          c.candidatecv_id,
                                          id,
                                          "Töökuulutus"
                                        )
                                      }
                                    >
                                      {/*2nd interview*/}
                                      {t("candidates.2ndInterview")}
                                    </a>
                                  ) : (
                                    <a
                                      className="dropdown-item"
                                      style={{ pointerEvents: "none" }}
                                    >
                                      {/*2nd interview*/}
                                      {t("candidates.2ndInterview")}
                                    </a>
                                  )}
                                  {c.action_id < 6 ? (
                                    <a
                                      className="dropdown-item"
                                      href="#"
                                      onClick={(event) =>
                                        candidateApplyToJob(
                                          6,
                                          c.candidatecv_id,
                                          id
                                        )
                                      }
                                    >
                                      {/*make offer*/}
                                      {t("candidates.makeJobOffer")}
                                    </a>
                                  ) : (
                                    <a
                                      className="dropdown-item"
                                      style={{ pointerEvents: "none" }}
                                    >
                                      {/*make offer*/}
                                      {t("candidates.makeJobOffer")}
                                    </a>
                                  )}
                                  {c.action_id < 7 ? (
                                    <a
                                      className="dropdown-item"
                                      href="#"
                                      onClick={(event) =>
                                        candidateApplyToJob(
                                          7,
                                          c.candidatecv_id,
                                          id,
                                          "Placed"
                                        )
                                      }
                                    >
                                      {/*make placement*/}
                                      {t("candidates.hire")}
                                    </a>
                                  ) : (
                                    <a
                                      className="dropdown-item"
                                      style={{ pointerEvents: "none" }}
                                    >
                                      {/*make placement*/}
                                      {t("candidates.hire")}
                                    </a>
                                  )}
                                  {c.action_id < 8 ? (
                                    <a
                                      className="dropdown-item"
                                      href="#"
                                      onClick={(event) => {
                                        candidateApplyToJob(
                                          8,
                                          c.candidatecv_id,
                                          id,
                                          "Kutse skype töövestlusele RUS"
                                        );
                                      }}
                                    >
                                      {/*rejected candidate*/}
                                      {t("candidates.reject")}
                                    </a>
                                  ) : (
                                    <a
                                      className="dropdown-item"
                                      style={{ pointerEvents: "none" }}
                                    >
                                      {/*rejected candidate*/}
                                      {t("candidates.reject")}
                                    </a>
                                  )}
                                </div>
                              </div>
                            </td>
                          </tr>
                        ))
                      ) : (
                        <NoRecordFound />
                      )}
                    </>
                  ) : (
                    <DataLoader />
                  )}
                </tbody>
              </table>
            </div>
          </CardBody>
          <Paginator
            totalRecords={totalRecords}
            pageLimit={10}
            pageNeighbours={2}
            setOffset={setOffset}
            currentPage={pageNumber}
            setCurrentPage={setPageNumber}
          />
        </Card>
      </Row>
    </Container>
  );
};

export default FilteredCandidates;
