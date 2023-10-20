import React, { useEffect, useState } from "react";
import "./candidate.css";
import Profile from "./Profile";
import Information from "./Information";
import Education from "./Education";
import Language from "./Language";
import Offers from "./Offers";
import Timeline from "./Timeline";
import Files from "./Files";
import { Button, Card, CardHeader, Container, Row } from "reactstrap";
import { useHistory, useLocation, useParams } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import AdditionalCourses from "./AdditionalCourses";
import Agreements from "./Agreements";
import { useTranslation } from "react-i18next";

const ViewCandidateProfile = (props) => {
  const userPermission = props.location.state;
  const { id } = useParams();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  const [tabsId, setTabsId] = useState("1");
  const [itemById, setItemById] = useState("");
  const [triggerUpdate, setTriggerUpdate] = useState();

  const setBookMart = (tabID) => {
    history.push(`/hr-candidate/view/${id}/${tabID}`);
    setTabsId(tabID);
  };

  const getCandidateSelector = useSelector(
    (state) => state.candidates.data.data
  );
  const settingsSelector = useSelector((state) => state.settings);

  return (
    <>
      <Container fluid={true}>
        <div className={"w-100 text-right mt-2"}>
          {location.state !== undefined ? (
            <>
              {location.state &&
              location.state.jobsState &&
              location.state.jobsState !== null ? (
                <Button
                  type={"button"}
                  color={"primary"}
                  onClick={() => history.push(location.state.jobsState)}
                >
                  {/*Tagasi tööpakkumiste nimekirja (Job)*/}
                  Tagasi tööpakkumiste nimekirja
                </Button>
              ) : location.state &&
                location.state.companyState &&
                location.state.companyState !== null ? (
                <Button
                  type={"button"}
                  color={"primary"}
                  onClick={() => {
                    history.push(location.state.companyState);
                  }}
                >
                  {/*Tagasi ettevõtete nimekirja (Company)*/}
                  Tagasi ettevõtete nimekirja
                </Button>
              ) : (
                <Button
                  type={"button"}
                  color={"primary"}
                  onClick={() => history.push("/hr-candidate")}
                >
                  {t("candidates.addNewCandidate.back")}
                </Button>
              )}
            </>
          ) : (
            <Button
              type={"button"}
              color={"primary"}
              onClick={() => history.push("/hr-candidate")}
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
              {t("candidates.addNewCandidate.back")}
            </Button>
          )}
        </div>
        <Card className="mt-2">
          <CardHeader className="float-right">
            <Container fluid={true}>
              <Row className={"justify-content-around"}>
                <div
                  className={
                    location.pathname === `/hr-candidate/view/${id}/1`
                      ? "active pointer"
                      : "pointer"
                  }
                  onClick={() => setBookMart("1")}
                >
                  {t("candidates.bookmarks.personalInformationBookmark")}
                </div>
                <div
                  className={
                    location.pathname === `/hr-candidate/view/${id}/2`
                      ? "active pointer"
                      : "pointer"
                  }
                  onClick={() => setBookMart("2")}
                >
                  {t("candidates.bookmarks.jobHistoryBookmark")}
                </div>
                <div
                  className={
                    location.pathname === `/hr-candidate/view/${id}/3`
                      ? "active pointer"
                      : "pointer"
                  }
                  onClick={() => setBookMart("3")}
                >
                  {t("candidates.bookmarks.educationBookmark")}
                </div>
                <div
                  className={
                    location.pathname === `/hr-candidate/view/${id}/4`
                      ? "active pointer"
                      : "pointer"
                  }
                  onClick={() => setBookMart("4")}
                >
                  {t("candidates.bookmarks.additionalCoursesBookmark")}
                </div>
                <div
                  className={
                    location.pathname === `/hr-candidate/view/${id}/5`
                      ? "active pointer"
                      : "pointer"
                  }
                  onClick={() => setBookMart("5")}
                >
                  {t("candidates.bookmarks.languagesBookmark")}
                </div>
                <div
                  className={
                    location.pathname === `/hr-candidate/view/${id}/6`
                      ? "active pointer"
                      : "pointer"
                  }
                  onClick={() => setBookMart("6")}
                >
                  {t("candidates.bookmarks.filesBookmark")}
                </div>
                <div
                  className={
                    location.pathname === `/hr-candidate/view/${id}/7`
                      ? "active pointer"
                      : "pointer"
                  }
                  onClick={() => setBookMart("7")}
                >
                  {t("candidates.bookmarks.agreementBookmark")}
                </div>
              </Row>
            </Container>
          </CardHeader>
        </Card>
        {location.pathname === `/hr-candidate/view/${id}/1` ? (
          <>
            <Profile
              // userPermission={userPermission}
              setTriggerUpdate={setTriggerUpdate}
            />
            <Timeline
              id={id}
              candidateItemById={itemById}
              triggerUpdate={triggerUpdate}
              setTriggerUpdate={setTriggerUpdate}
            />
            <Offers
              // id={id}
              candidateItemById={itemById}
              triggerUpdate={triggerUpdate}
              setTriggerUpdate={setTriggerUpdate}
            />
          </>
        ) : location.pathname === `/hr-candidate/view/${id}/2` ? (
          <Information candidateID={id} />
        ) : location.pathname === `/hr-candidate/view/${id}/3` ? (
          <Education candidateID={id} />
        ) : location.pathname === `/hr-candidate/view/${id}/4` ? (
          <AdditionalCourses candidateCvId={id} candidateID={id} />
        ) : location.pathname === `/hr-candidate/view/${id}/5` ? (
          <Language candidateCvId={id} />
        ) : location.pathname === `/hr-candidate/view/${id}/6` ? (
          <Files candidateCvId={id} />
        ) : (
          <Agreements />
        )}
      </Container>
    </>
  );
};

export default ViewCandidateProfile;
