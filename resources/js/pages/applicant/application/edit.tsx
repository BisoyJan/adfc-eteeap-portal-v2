import { useForm } from '@inertiajs/react';
import { Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import ApplicationController from '@/actions/App/Http/Controllers/Applicant/ApplicationController';
import EducationRecordController from '@/actions/App/Http/Controllers/Applicant/EducationRecordController';
import ExperienceRecordController from '@/actions/App/Http/Controllers/Applicant/ExperienceRecordController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';
import type { ApplicantProfile, Application, EducationRecord, ExperienceRecord } from '@/types';

type PageProps = {
    application: Application;
    profile: ApplicantProfile | null;
    experienceRecords: ExperienceRecord[];
    educationRecords: EducationRecord[];
    qualificationIssues: string[];
};

export default function ApplicationEdit({ application, profile, experienceRecords, educationRecords, qualificationIssues }: PageProps) {
    const profileForm = useForm({
        birth_date: profile?.birth_date ?? '',
        citizenship: profile?.citizenship ?? 'Filipino',
        civil_status: profile?.civil_status ?? '',
        gender: profile?.gender ?? '',
        address: profile?.address ?? '',
        highest_education: profile?.highest_education ?? '',
        last_school_attended: profile?.last_school_attended ?? '',
    });

    const experienceForm = useForm({
        company: '',
        position: '',
        start_date: '',
        end_date: '',
        is_current: false as boolean,
        description: '',
        bsit_aligned: false as boolean,
    });

    const educationForm = useForm({
        school_name: '',
        degree: '',
        field_of_study: '',
        start_year: '',
        end_year: '',
        is_graduated: false as boolean,
        education_level: 'college',
    });

    function saveProfile(event: FormEvent) {
        event.preventDefault();
        profileForm.put(ApplicationController.update.url());
    }

    function addExperience(event: FormEvent) {
        event.preventDefault();
        experienceForm.post(ExperienceRecordController.store.url(), {
            preserveScroll: true,
            onSuccess: () => experienceForm.reset(),
        });
    }

    function removeExperience(id: number) {
        experienceForm.delete(ExperienceRecordController.destroy.url(id), { preserveScroll: true });
    }

    function addEducation(event: FormEvent) {
        event.preventDefault();
        educationForm.post(EducationRecordController.store.url(), {
            preserveScroll: true,
            onSuccess: () => educationForm.reset(),
        });
    }

    function removeEducation(id: number) {
        educationForm.delete(EducationRecordController.destroy.url(id), { preserveScroll: true });
    }

    function submitApplication() {
        router.post(ApplicationController.submit.url(), {}, { preserveScroll: true });
    }

    return (
        <>
            <Head title="My Application" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title={`Application ${application.application_number}`} description={`Status: ${application.status.replace('_', ' ')}`} />

                {qualificationIssues.length > 0 && (
                    <div className="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-200/10 dark:bg-amber-700/10 dark:text-amber-200">
                        <p className="font-medium">Before you can submit:</p>
                        <ul className="mt-2 list-inside list-disc">
                            {qualificationIssues.map((issue) => (
                                <li key={issue}>{issue}</li>
                            ))}
                        </ul>
                    </div>
                )}

                <Card>
                    <CardHeader>
                        <CardTitle>Personal & Education Profile</CardTitle>
                    </CardHeader>
                    <form onSubmit={saveProfile}>
                        <CardContent className="grid gap-4 sm:grid-cols-2">
                            <div className="grid gap-2">
                                <Label htmlFor="birth_date">Birth date</Label>
                                <Input
                                    id="birth_date"
                                    type="date"
                                    value={profileForm.data.birth_date}
                                    onChange={(e) => profileForm.setData('birth_date', e.target.value)}
                                />
                                <InputError message={profileForm.errors.birth_date} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="citizenship">Citizenship</Label>
                                <Input
                                    id="citizenship"
                                    value={profileForm.data.citizenship}
                                    onChange={(e) => profileForm.setData('citizenship', e.target.value)}
                                />
                                <InputError message={profileForm.errors.citizenship} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="civil_status">Civil status</Label>
                                <Input
                                    id="civil_status"
                                    placeholder="single, married, widowed, separated"
                                    value={profileForm.data.civil_status}
                                    onChange={(e) => profileForm.setData('civil_status', e.target.value)}
                                />
                                <InputError message={profileForm.errors.civil_status} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="gender">Gender</Label>
                                <Input
                                    id="gender"
                                    placeholder="male, female, prefer_not_to_say"
                                    value={profileForm.data.gender}
                                    onChange={(e) => profileForm.setData('gender', e.target.value)}
                                />
                                <InputError message={profileForm.errors.gender} />
                            </div>

                            <div className="grid gap-2 sm:col-span-2">
                                <Label htmlFor="address">Address</Label>
                                <Textarea
                                    id="address"
                                    value={profileForm.data.address}
                                    onChange={(e) => profileForm.setData('address', e.target.value)}
                                />
                                <InputError message={profileForm.errors.address} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="highest_education">Highest education attained</Label>
                                <Input
                                    id="highest_education"
                                    value={profileForm.data.highest_education}
                                    onChange={(e) => profileForm.setData('highest_education', e.target.value)}
                                />
                                <InputError message={profileForm.errors.highest_education} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="last_school_attended">Last school attended</Label>
                                <Input
                                    id="last_school_attended"
                                    value={profileForm.data.last_school_attended}
                                    onChange={(e) => profileForm.setData('last_school_attended', e.target.value)}
                                />
                                <InputError message={profileForm.errors.last_school_attended} />
                            </div>
                        </CardContent>
                        <CardFooter>
                            <Button disabled={profileForm.processing}>Save profile</Button>
                        </CardFooter>
                    </form>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Work Experience</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {experienceRecords.map((record) => (
                            <div key={record.id} className="flex items-start justify-between rounded-lg border p-3">
                                <div>
                                    <p className="font-medium">
                                        {record.position} &middot; {record.company}
                                    </p>
                                    <p className="text-muted-foreground text-sm">
                                        {record.start_date} &ndash; {record.is_current ? 'Present' : (record.end_date ?? '')}
                                    </p>
                                </div>
                                <Button type="button" variant="outline" onClick={() => removeExperience(record.id)}>
                                    Remove
                                </Button>
                            </div>
                        ))}

                        <form onSubmit={addExperience} className="grid gap-4 rounded-lg border border-dashed p-3 sm:grid-cols-2">
                            <div className="grid gap-2">
                                <Label htmlFor="company">Company</Label>
                                <Input
                                    id="company"
                                    value={experienceForm.data.company}
                                    onChange={(e) => experienceForm.setData('company', e.target.value)}
                                />
                                <InputError message={experienceForm.errors.company} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="position">Position</Label>
                                <Input
                                    id="position"
                                    value={experienceForm.data.position}
                                    onChange={(e) => experienceForm.setData('position', e.target.value)}
                                />
                                <InputError message={experienceForm.errors.position} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="start_date">Start date</Label>
                                <Input
                                    id="start_date"
                                    type="date"
                                    value={experienceForm.data.start_date}
                                    onChange={(e) => experienceForm.setData('start_date', e.target.value)}
                                />
                                <InputError message={experienceForm.errors.start_date} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="end_date">End date</Label>
                                <Input
                                    id="end_date"
                                    type="date"
                                    disabled={experienceForm.data.is_current}
                                    value={experienceForm.data.end_date}
                                    onChange={(e) => experienceForm.setData('end_date', e.target.value)}
                                />
                                <InputError message={experienceForm.errors.end_date} />
                            </div>
                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="is_current"
                                    checked={experienceForm.data.is_current}
                                    onCheckedChange={(checked) => experienceForm.setData('is_current', checked === true)}
                                />
                                <Label htmlFor="is_current">I currently work here</Label>
                            </div>
                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="bsit_aligned"
                                    checked={experienceForm.data.bsit_aligned}
                                    onCheckedChange={(checked) => experienceForm.setData('bsit_aligned', checked === true)}
                                />
                                <Label htmlFor="bsit_aligned">Aligned with BSIT competencies</Label>
                            </div>
                            <div className="grid gap-2 sm:col-span-2">
                                <Label htmlFor="description">Description</Label>
                                <Textarea
                                    id="description"
                                    value={experienceForm.data.description}
                                    onChange={(e) => experienceForm.setData('description', e.target.value)}
                                />
                                <InputError message={experienceForm.errors.description} />
                            </div>
                            <div className="sm:col-span-2">
                                <Button type="submit" disabled={experienceForm.processing}>
                                    Add experience
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Education</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {educationRecords.map((record) => (
                            <div key={record.id} className="flex items-start justify-between rounded-lg border p-3">
                                <div>
                                    <p className="font-medium">{record.school_name}</p>
                                    <p className="text-muted-foreground text-sm">
                                        {record.degree ?? record.education_level} &middot; {record.start_year}&ndash;
                                        {record.end_year ?? 'Present'}
                                    </p>
                                </div>
                                <Button type="button" variant="outline" onClick={() => removeEducation(record.id)}>
                                    Remove
                                </Button>
                            </div>
                        ))}

                        <form onSubmit={addEducation} className="grid gap-4 rounded-lg border border-dashed p-3 sm:grid-cols-2">
                            <div className="grid gap-2">
                                <Label htmlFor="school_name">School name</Label>
                                <Input
                                    id="school_name"
                                    value={educationForm.data.school_name}
                                    onChange={(e) => educationForm.setData('school_name', e.target.value)}
                                />
                                <InputError message={educationForm.errors.school_name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="education_level">Education level</Label>
                                <Input
                                    id="education_level"
                                    placeholder="elementary, high_school, vocational, college, graduate"
                                    value={educationForm.data.education_level}
                                    onChange={(e) => educationForm.setData('education_level', e.target.value)}
                                />
                                <InputError message={educationForm.errors.education_level} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="degree">Degree</Label>
                                <Input
                                    id="degree"
                                    value={educationForm.data.degree}
                                    onChange={(e) => educationForm.setData('degree', e.target.value)}
                                />
                                <InputError message={educationForm.errors.degree} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="field_of_study">Field of study</Label>
                                <Input
                                    id="field_of_study"
                                    value={educationForm.data.field_of_study}
                                    onChange={(e) => educationForm.setData('field_of_study', e.target.value)}
                                />
                                <InputError message={educationForm.errors.field_of_study} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="start_year">Start year</Label>
                                <Input
                                    id="start_year"
                                    type="number"
                                    value={educationForm.data.start_year}
                                    onChange={(e) => educationForm.setData('start_year', e.target.value)}
                                />
                                <InputError message={educationForm.errors.start_year} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="end_year">End year</Label>
                                <Input
                                    id="end_year"
                                    type="number"
                                    value={educationForm.data.end_year}
                                    onChange={(e) => educationForm.setData('end_year', e.target.value)}
                                />
                                <InputError message={educationForm.errors.end_year} />
                            </div>
                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="is_graduated"
                                    checked={educationForm.data.is_graduated}
                                    onCheckedChange={(checked) => educationForm.setData('is_graduated', checked === true)}
                                />
                                <Label htmlFor="is_graduated">Graduated</Label>
                            </div>
                            <div className="sm:col-span-2">
                                <Button type="submit" disabled={educationForm.processing}>
                                    Add education
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <div className="flex justify-end">
                    <Button onClick={submitApplication} disabled={qualificationIssues.length > 0}>
                        Submit application
                    </Button>
                </div>
            </div>
        </>
    );
}

ApplicationEdit.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }, { title: 'My Application', href: ApplicationController.edit() }],
};
