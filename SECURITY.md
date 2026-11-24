# Symbiota Responsible Security Vulnerability Disclosure Policy

**Effective Date:** 11/1/2026
**Last Updated:** 11/1/2026
**Maintained By the Symbiota Support Hub - Biodiversity Institute & Natural History Museum - University of Kansas**

## **1\. Our Commitment to Security**

The Symbiota project takes the security of its software and the safety of its users seriously.

We recognize that security vulnerabilities are an inevitable part of software development and that independent security researchers, users, and contributors play an important role in identifying and helping us address them.

We welcome good-faith security research and encourage anyone who discovers a potential security vulnerability in our software to report it to us responsibly.

This policy establishes a process for reporting, receiving, investigating, and resolving security vulnerabilities while minimizing risk to project users and the broader open-source community.

As a community-maintained open-source project, our security response capabilities may be limited. We are not a dedicated security organization, and our maintainers may be volunteers.

## **2\. Scope**

This policy applies to security vulnerabilities affecting software and resources maintained by the Symbiota project.

### **In-Scope**

In-scope assets include:

-   Official source-code repositories maintained by Symbiota.

-   Official released versions of the software.

-   Project-maintained build scripts, installation scripts, and distribution packages.

-   Project-maintained infrastructure and services, where applicable.

-   Security-sensitive functionality implemented directly by Symbiota.

A vulnerability is generally considered in scope if it has a demonstrable security impact on the confidentiality, integrity, or availability of the Symbiota codebase, Symbiota portals, or its users.

Examples of in-scope vulnerabilities include:

-   Authentication or authorization bypasses.

-   Privilege escalation.

-   Arbitrary code execution.

-   Remote code execution.

-   Injection vulnerabilities.

-   Insecure handling of sensitive information, credentials, or secrets.

-   Path traversal and arbitrary file access.

-   Cryptographic implementation flaws.

-   Insecure deserialization.

-   Security-relevant dependency or supply-chain vulnerabilities introduced or improperly handled by Symbiota.

-   Other vulnerabilities that could reasonably expose users or systems to security risks.

### **Out of Scope**

The following are generally outside the scope of this policy unless they demonstrate a specific, meaningful security impact:

-   Vulnerabilities in third-party software or services that are not maintained by this project.

-   Issues that have already been publicly reported or documented.

-   General software bugs without a security impact.

-   Theoretical vulnerabilities without a reasonable demonstration of security impact.

-   Social engineering, phishing, or attacks against project maintainers or contributors.

-   Denial-of-service testing that could disrupt services, infrastructure, or other users.

-   Physical attacks against systems or infrastructure.

-   Issues resulting solely from unsupported software versions or insecure configurations outside Symbiota's control.

If you are uncertain whether a finding is in scope, we encourage you to contact us before proceeding with further testing.

## **3\. Reporting a Security Vulnerability**

**Please report suspected security vulnerabilities privately.**

Do not disclose suspected vulnerabilities through public GitHub issues, discussions, pull requests, or other public communication channels before coordinating with Symbiota maintainers.

Reports should be submitted through one of the following channels:

**Reporting method:**

-   GitHub Private Vulnerability Reporting: [https://github.com/Symbiota/Symbiota/security/advisories/new](https://github.com/Symbiota/Symbiota/security/advisories/new)

Please do not include sensitive vulnerability details in a public issue simply to request a private reporting channel.

### **Information to Include in Your Report**

To help us understand and investigate a reported vulnerability, please include as much of the following information as possible:

1.  **Description:** A clear explanation of the suspected vulnerability.

2.  **Affected Versions:** The versions, branches, or commits believed to be affected.

3.  **Affected Components:** Relevant source files, functions, modules, or components.

4.  **Security Impact:** An explanation of the potential consequences and how an attacker might exploit the issue.

5.  **Reproduction Steps:** Clear, step-by-step instructions for reproducing the issue.

6.  **Proof of Concept:** A minimal proof of concept or demonstration, if available.

7.  **Environment:** Relevant operating systems, configurations, dependencies, or other environmental details.

8.  **Suggested Remediation:** Any proposed fixes or mitigations, if available.

A complete report is appreciated, but we do not require every item above. Please do not delay reporting a legitimate vulnerability simply because some information is unavailable.

We may request additional information to help validate or investigate a report.

## **4\. Researcher Guidelines**

We encourage security researchers to conduct good-faith security research that helps improve the security of Symbiota and protects its users.

When investigating or reporting a potential vulnerability, we ask researchers to:

-   Make a reasonable effort to avoid accessing, modifying, deleting, or exposing data belonging to others.

-   Avoid disrupting project infrastructure, services, or the availability of software used by others.

-   Avoid testing against systems or services that you do not own or have explicit authorization to test.

-   Avoid exploiting a vulnerability beyond what is reasonably necessary to demonstrate its existence and potential impact.

-   Avoid installing persistent access mechanisms, malware, or other tools on systems.

-   Avoid publicly disclosing vulnerability details before allowing the maintainers a reasonable opportunity to investigate and address the issue.

-   Stop testing and notify the maintainers if sensitive information or unintended access to another person's data is encountered.

Researchers should use isolated test environments and locally controlled systems whenever reasonably possible.

We appreciate reports that include clear technical evidence without unnecessarily exposing users or infrastructure to risk.

## **5\. Maintainer Response and Handling Process**

Symbiota maintainers will make reasonable efforts to handle vulnerability reports promptly and confidentially.

Because this project may be maintained by volunteers with limited availability, the following timelines represent our response goals rather than guaranteed service-level commitments.

### **Initial Acknowledgment**

We aim to acknowledge receipt of a vulnerability report within **5 business days**.

The acknowledgment will confirm that the report has been received and is being reviewed.

If additional time is required, we will communicate that to the reporter when reasonably possible.

### **Initial Assessment and Triage**

We aim to perform an initial assessment within **14 calendar days** of receiving a sufficiently detailed report.

During this assessment, maintainers will attempt to:

-   Confirm whether the reported behavior is reproducible.

-   Determine whether the issue represents a security vulnerability.

-   Identify affected versions and components.

-   Assess the potential severity and impact.

-   Determine whether temporary mitigations or immediate action are necessary.

If the report is incomplete or requires additional investigation, we may request further information from the reporter.

Reports that cannot be reproduced or do not demonstrate a security impact may be classified as non-security issues, with an explanation provided where reasonably possible.

### **Remediation**

Once a vulnerability has been confirmed, maintainers will make reasonable efforts to develop, test, and release an appropriate fix.

Our target is to provide a fix or meaningful mitigation within **90 calendar days** of the initial report.

Actual remediation times may vary depending on:

-   Vulnerability severity and complexity.

-   The availability of maintainers and contributors.

-   The difficulty of developing and validating a fix.

-   Dependencies on upstream projects or external maintainers.

-   The potential risk of introducing regressions.

-   The availability of affected software versions and release mechanisms.

Critical vulnerabilities that pose an immediate risk to users will be prioritized for expedited investigation and remediation.

## **6\. Coordinated Disclosure**

We follow a coordinated vulnerability disclosure approach.

This means that the reporter and project maintainers work together, where reasonably possible, to investigate and address a vulnerability before publicly disclosing technical details.

Our general disclosure process is:

1.  The researcher privately submits a vulnerability report.

2.  Maintainers acknowledge and investigate the report.

3.  The vulnerability is assessed and validated.

4.  Maintainers develop and test a fix or mitigation.

5.  A corrected release or other appropriate mitigation is made available.

6.  Maintainers publish a security advisory describing the vulnerability and its remediation.

7.  The researcher is credited for their contribution, if they wish to be identified.

### **Disclosure Timeline**

We generally aim for coordinated public disclosure within **90 calendar days** of receiving a valid vulnerability report.

The disclosure period may be extended when necessary to allow reasonable remediation, particularly for complex or high-impact vulnerabilities.

Any extension should be discussed with the reporter, with a revised target date established where possible.

We do not expect researchers to withhold vulnerability information indefinitely.

If maintainers become unresponsive or cannot provide a reasonable remediation plan, researchers may proceed with responsible public disclosure after making reasonable efforts to coordinate with Symbiota.

We ask that any public disclosure avoid unnecessary exposure of users to active exploitation and provide sufficient information for affected users to understand and mitigate the risk.

### **Public Disclosure**

Once coordinated disclosure is appropriate, maintainers will make reasonable efforts to:

-   Publish a security advisory through Symbiota's official communication channels.

-   Identify affected versions and components.

-   Describe the vulnerability and its potential impact.

-   Identify fixed versions and available mitigations.

-   Provide upgrade or remediation instructions.

-   Credit the reporting researcher, if requested.

Where appropriate, maintainers may request or obtain a CVE identifier for the vulnerability.

Security advisories may be published through GitHub Security Advisories or another appropriate public vulnerability disclosure platform.

## **7\. Confidentiality and Communication**

Maintainers will make reasonable efforts to keep vulnerability reports confidential until coordinated disclosure is appropriate.

Access to vulnerability reports and related communications will be limited to maintainers and contributors who reasonably need the information to investigate or remediate the issue.

We ask researchers to respect the confidentiality of vulnerability details during the coordination period.

Both parties should communicate professionally and in good faith throughout the disclosure process.

We will make reasonable efforts to keep reporters informed of significant developments, including:

-   Confirmation or rejection of a vulnerability report.

-   Changes to the assessment of severity or impact.

-   Identification of affected versions.

-   Availability of a fix or mitigation.

-   Changes to the anticipated disclosure date.

We understand that researchers may wish to remain anonymous. We will respect reasonable requests for anonymity and will not publicly identify a researcher without their permission.

## **8\. Researcher Recognition and Compensation**

We appreciate the time and effort invested by individuals who responsibly report security vulnerabilities.

Researchers who contribute to improving the security of Symbiota may be acknowledged in security advisories, release notes, or other project communications, subject to their preferences.

**Symbiota does not currently operate a paid bug bounty program.**

Submission of a vulnerability report does not create an entitlement to financial compensation, rewards, or other remuneration.

We nevertheless value responsible security research and recognize that meaningful contributions help protect the entire project community.

## **9\. Safe Harbor**

The Symbiota project considers security research conducted in accordance with this policy to be authorized good-faith activity within the scope described here.

To the extent of our authority and control, we will not initiate or support legal action against researchers who:

-   Make a good-faith effort to comply with this policy.

-   Conduct research limited to the scope described in this policy.

-   Avoid unnecessary harm to users, systems, and infrastructure.

-   Report discovered vulnerabilities privately and in a timely manner.

-   Make reasonable efforts to coordinate disclosure with the Symbiota team.

We consider activities conducted consistently with this policy to be authorized for purposes of Symbiota's own enforcement decisions.

This commitment applies only to actions Symbiota and its maintainers have the legal authority to control. It does not grant permission to access third-party systems, services, accounts, or infrastructure.

This policy does not override applicable laws or third-party terms of service, nor does it provide immunity from legal claims brought by third parties.

If a researcher inadvertently encounters information or access beyond what is necessary to demonstrate a vulnerability, we ask that they stop testing, avoid further access or disclosure, and notify the maintainers promptly.

## **10\. Policy Limitations**

This policy describes Symbiota's intended security disclosure practices and communication expectations.

It does not constitute a guarantee that every reported vulnerability will be fixed, that a particular remediation timeline will be met, or that Symbiota will provide ongoing security support for all historical releases.

Symbiota reserves the right to update this policy as its maintenance capabilities, infrastructure, and security practices evolve.

Any significant policy changes will be published in Symbiota's official repository.

## **11\. Acknowledgments**

We appreciate the security researchers, users, and contributors who help identify and responsibly report vulnerabilities.

Responsible disclosure is an important part of maintaining a healthy open-source ecosystem. We welcome constructive collaboration and are grateful to everyone who helps make Symbiota safer and more secure.

**Thank you for helping us protect Symbiota and its users.**

## **12\. References and Best-Practice Foundations**

This policy draws on established open-source vulnerability disclosure guidance:

1.  OpenSSF – [Guide to implementing a coordinated vulnerability disclosure process for open-source projects](https://github.com/ossf/oss-vulnerability-guide/blob/main/maintainer-guide.md#readme)

2.  Google – [Open Source Vulnerability Guide](https://github.com/google/oss-vulnerability-guide)

3.  GitHub – [Coordinated disclosure of security vulnerabilities](https://docs.github.com/en/code-security/concepts/vulnerability-reporting-and-management/coordinated-disclosure)

4.  CERT Coordination Center – [Vulnerability Disclosure Policy Templates](https://certcc.github.io/CERT-Guide-to-CVD/reference/policy_templates/)