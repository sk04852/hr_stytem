import React, { useState } from "react";
import EmailTemplateEditor from "./EmailTemplateEditor";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Container,
  Row,
} from "reactstrap";
import { useHistory } from "react-router-dom";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import Wildcards from "./Wildcards";

const AddEmailTemplate = (props) => {
  const { fixNavbar } = props;
  const history = useHistory();

  const { t } = useTranslation();

  const [body, setBody] = useState("");
  const [activeField, setActiveField] = useState(null);

  const settingsSelector = useSelector((state) => state.settings);

  return (
    <div className={`section-body ${fixNavbar ? "marginTop" : ""} mt-3`}>
      <Container fluid={true}>
        <Row className="clearfix">
          <Col md={12}>
            <div className={"text-right mb-3 mr-3"}>
              <Button
                type={"button"}
                color={"primary"}
                onClick={() => history.push("/jobportal-settings/3")}
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
                {t("buttons.back")}
              </Button>
            </div>
          </Col>
          <Col md={8}>
            <Card>
              <CardHeader>
                <CardTitle
                  className={"font-weight-bold"}
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
                  {t("settings.emailTemplates.addNewTemplate")}
                </CardTitle>
              </CardHeader>
              <CardBody>
                <div className="summernote">
                  <EmailTemplateEditor
                    body={body}
                    setBody={setBody}
                    activeField={activeField}
                    setActiveField={setActiveField}
                  />
                </div>
              </CardBody>
            </Card>
          </Col>
          <Wildcards
            activeField={activeField}
            body={body}
            setBody={setBody}
            disabledCards={false}
          />
        </Row>
      </Container>
    </div>
  );
};

export default AddEmailTemplate;
