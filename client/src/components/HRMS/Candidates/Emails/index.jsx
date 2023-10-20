import React from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  Container,
  Row,
} from "reactstrap";
import { NavLink, useHistory, useLocation, useParams } from "react-router-dom";
import EmailEditor from "./EmailEditor";
import { useTranslation } from "react-i18next";

const SendingEmails = (props) => {
  const { fixNavbar } = props;
  const { id, template_key } = useParams();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  return (
    <>
      <div className={`section-body ${fixNavbar ? "marginTop" : ""} mt-3`}>
        <Container fluid={true}>
          <Row className="clearfix">
            <Col md={12}>
              <div className={"text-right mb-3 mr-3"}>
                {location.state?.backToCandidateList ? (
                  <Button
                    type={"submit"}
                    color={"primary"}
                    onClick={() => history.push("/hr-candidate")}
                  >
                    {t("candidates.addNewCandidate.back")}
                  </Button>
                ) : location.state?.jobCandidates ? (
                  <Button
                    type={"submit"}
                    color={"primary"}
                    onClick={() => history.push(location.state?.jobCandidates)}
                  >
                    {t("jobs.backToViewJobPage")}
                  </Button>
                ) : (
                  <Button
                    type={"button"}
                    color={"primary"}
                    onClick={() => history.push(`/hr-candidate/view/${id}/1`)}
                  >
                    {t("buttons.back")}
                  </Button>
                )}
              </div>
            </Col>
            <Col md={12}>
              <div className="container-fluid">
                <ul className="nav nav-tabs page-header-tab">
                  <li className="nav-item">
                    <NavLink
                      to="/hr-candidate/sending-emails"
                      className="nav-link active"
                    >
                      {t("sendEmailsTabs.candidateTab")}
                    </NavLink>
                  </li>
                </ul>
              </div>
              <Card>
                <CardHeader></CardHeader>
                <CardBody>
                  <div className="summernote">
                    <EmailEditor template_key={template_key} />
                  </div>
                </CardBody>
              </Card>
            </Col>
          </Row>
        </Container>
      </div>
    </>
  );
};

export default SendingEmails;
