import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import api from '../../api/client';
import { HiOutlineTrash, HiOutlinePlus } from 'react-icons/hi';

const emptyAddress = { street: '', rt: '', rw: '', village: '', district: '', city: '', province: '', postal_code: '' };
const emptyGuardian = { person: { name: '', occupation: '', education: '' }, relationship: 'father' };

export default function BeneficiaryForm() {
    const navigate = useNavigate();
    const { id } = useParams();
    const isEdit = !!id;

    const [form, setForm] = useState({
        nik: '',
        type: 'child',
        full_name: '',
        nick_name: '',
        birth_place: '',
        birth_date: '',
        gender: 'male',
        family_card: { number: '', head_of_family_name: '', address: { ...emptyAddress } },
        guardians: [{ ...emptyGuardian }],
        specific_attributes: null,
        child_attributes: {
            education: {
                level: 'elementary',
                status: 'currently_enrolled',
                schoolName: '',
                grade: 1,
                major: '',
                nisn: '',
            },
        },
        is_active: true,
    });
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState({});

    useEffect(() => {
        if (isEdit) {
            setLoading(true);
            api.get(`/beneficiaries/${id}`)
                .then(res => {
                    const d = res.data.data || res.data;
                    setForm(prev => ({
                        ...prev,
                        nik: d.nik || '',
                        type: d.type || 'child',
                        full_name: d.full_name || '',
                        nick_name: d.nick_name || '',
                        birth_place: d.birth_place || '',
                        birth_date: d.birth_date || '',
                        gender: d.gender || 'male',
                        family_card: d.family_card || d.familyCard || prev.family_card,
                        guardians: d.guardians && d.guardians.length > 0 ? d.guardians : prev.guardians,
                        specific_attributes: d.specific_attributes || null,
                        child_attributes: d.specific_attributes ? {
                            education: {
                                level: d.specific_attributes.education?.level || 'elementary',
                                status: d.specific_attributes.education?.status || 'currently_enrolled',
                                schoolName: d.specific_attributes.education?.schoolName || '',
                                grade: d.specific_attributes.education?.grade || 1,
                                major: d.specific_attributes.education?.major || '',
                                nisn: d.specific_attributes.education?.nisn || '',
                            },
                        } : prev.child_attributes,
                        is_active: d.is_active !== undefined ? d.is_active : true,
                    }));
                })
                .catch(() => navigate('/beneficiaries'))
                .finally(() => setLoading(false));
        }
    }, [id, isEdit, navigate]);

    const update = (field, value) => setForm(prev => ({ ...prev, [field]: value }));
    const updateFC = (field, value) => setForm(prev => ({
        ...prev,
        family_card: {
            ...prev.family_card,
            [field]: value,
            address: field.startsWith('addr_')
                ? { ...prev.family_card.address, [field.replace('addr_', '')]: value }
                : prev.family_card.address
        }
    }));

    const updateChildAttr = (field, value) => setForm(prev => {
        const ca = { ...prev.child_attributes };
        if (field.startsWith('education.')) {
            ca.education = { ...ca.education, [field.replace('education.', '')]: value };
        } else {
            ca[field] = value;
        }
        return { ...prev, child_attributes: ca };
    });

    const updateGuardian = (idx, field, value) => {
        const guardians = [...form.guardians];
        if (field.startsWith('person.')) {
            const personField = field.replace('person.', '');
            guardians[idx] = { ...guardians[idx], person: { ...guardians[idx].person, [personField]: value } };
        } else {
            guardians[idx] = { ...guardians[idx], [field]: value };
        }
        setForm(prev => ({ ...prev, guardians }));
    };

    const addGuardian = () => setForm(prev => ({ ...prev, guardians: [...prev.guardians, { ...emptyGuardian }] }));
    const removeGuardian = (idx) => {
        if (form.guardians.length <= 1) return;
        setForm(prev => ({ ...prev, guardians: prev.guardians.filter((_, i) => i !== idx) }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        const payload = {
            nik: form.nik,
            type: form.type,
            fullName: form.full_name,
            nickName: form.nick_name || null,
            birthPlace: form.birth_place,
            birthDate: form.birth_date,
            gender: form.gender,
            familyCard: {
                number: form.family_card.number,
                head_of_family_name: form.family_card.head_of_family_name,
                address: (form.family_card.address?.street && form.family_card.address?.village && form.family_card.address?.district && form.family_card.address?.city && form.family_card.address?.province)
                    ? {
                        street: form.family_card.address.street,
                        rt: form.family_card.address.rt || '',
                        rw: form.family_card.address.rw || '',
                        village: form.family_card.address.village,
                        district: form.family_card.address.district,
                        city: form.family_card.address.city,
                        province: form.family_card.address.province,
                        postal_code: form.family_card.address.postal_code || '',
                    }
                    : null,
            },
            guardians: form.guardians.filter(g => g.person.name).map(g => ({
                person: {
                    name: g.person.name,
                    occupation: g.person.occupation || null,
                    education: g.person.education || null,
                    address: null,
                    contact: null,
                },
                relationship: g.relationship,
            })),
            specificAttributes: form.type === 'child' ? {
                education: {
                    level: form.child_attributes.education.level,
                    status: form.child_attributes.education.status,
                    schoolName: form.child_attributes.education.schoolName,
                    grade: parseInt(form.child_attributes.education.grade) || 1,
                    major: form.child_attributes.education.major || null,
                    nisn: form.child_attributes.education.nisn || null,
                },
            } : null,
            isActive: form.is_active,
        };

        try {
            if (isEdit) {
                await api.put(`/beneficiaries/${id}`, payload);
            } else {
                await api.post('/beneficiaries', payload);
            }
            navigate('/beneficiaries');
        } catch (error) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            } else if (error.response?.data?.message) {
                setErrors({ _general: error.response.data.message });
            }
        } finally {
            setLoading(false);
        }
    };

    const inputClass = (field) =>
        `w-full px-3 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500 focus:border-transparent ${
            errors[field] ? 'border-red-500' : 'border-gray-200 dark:border-gray-600'}`;

    if (loading && isEdit) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-cyan-600"></div>
            </div>
        );
    }

    return (
        <div>
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                    {isEdit ? 'Edit Beneficiary' : 'Add New Beneficiary'}
                </h1>
            </div>

            <form onSubmit={handleSubmit} className="space-y-8">
                {errors._general && (
                    <div className="p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">
                        {errors._general}
                    </div>
                )}

                {/* Personal Information */}
                <section className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK *</label>
                            <input type="text" value={form.nik} onChange={e => update('nik', e.target.value)}
                                className={inputClass('nik')} placeholder="16-digit NIK" required />
                            {errors.nik && <p className="text-red-500 text-xs mt-1">{errors.nik}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                            <select value={form.type} onChange={e => update('type', e.target.value)}
                                className={inputClass('type')} required>
                                <option value="child">Child</option>
                                <option value="elderly">Elderly</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </div>
                        <div className="md:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name *</label>
                            <input type="text" value={form.full_name} onChange={e => update('full_name', e.target.value)}
                                className={inputClass('full_name')} placeholder="Full name" required />
                            {errors.full_name && <p className="text-red-500 text-xs mt-1">{errors.full_name}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nick Name</label>
                            <input type="text" value={form.nick_name} onChange={e => update('nick_name', e.target.value)}
                                className={inputClass('nick_name')} placeholder="Nickname (optional)" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender *</label>
                            <select value={form.gender} onChange={e => update('gender', e.target.value)}
                                className={inputClass('gender')} required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birth Place *</label>
                            <input type="text" value={form.birth_place} onChange={e => update('birth_place', e.target.value)}
                                className={inputClass('birth_place')} placeholder="City of birth" required />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birth Date *</label>
                            <input type="date" value={form.birth_date} onChange={e => update('birth_date', e.target.value)}
                                className={inputClass('birth_date')} required />
                        </div>
                    </div>
                    {form.type === 'child' && (
                        <div className="mt-6 border-t border-gray-200 dark:border-gray-600 pt-6">
                            <h3 className="text-md font-semibold text-gray-900 dark:text-white mb-4">Child Education Details</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Education Level *</label>
                                    <select value={form.child_attributes.education.level}
                                        onChange={e => updateChildAttr('education.level', e.target.value)}
                                        className={inputClass('child_attributes.education.level')} required>
                                        <option value="none">None</option>
                                        <option value="elementary">Elementary</option>
                                        <option value="junior_high">Junior High</option>
                                        <option value="senior_high">Senior High</option>
                                        <option value="diploma">Diploma</option>
                                        <option value="bachelor">Bachelor</option>
                                        <option value="master">Master</option>
                                        <option value="doctor">Doctor</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                                    <select value={form.child_attributes.education.status}
                                        onChange={e => updateChildAttr('education.status', e.target.value)}
                                        className={inputClass('child_attributes.education.status')} required>
                                        <option value="currently_enrolled">Currently Enrolled</option>
                                        <option value="enrolled">Enrolled</option>
                                        <option value="not_enrolled">Not Enrolled</option>
                                        <option value="graduated">Graduated</option>
                                        <option value="transferred">Transferred</option>
                                        <option value="dropped_out">Dropped Out</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">School Name *</label>
                                    <input type="text" value={form.child_attributes.education.schoolName}
                                        onChange={e => updateChildAttr('education.schoolName', e.target.value)}
                                        className={inputClass('child_attributes.education.schoolName')}
                                        placeholder="School name" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Grade *</label>
                                    <input type="number" min="1" max="12" value={form.child_attributes.education.grade}
                                        onChange={e => updateChildAttr('education.grade', e.target.value)}
                                        className={inputClass('child_attributes.education.grade')}
                                        placeholder="1-12" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Major</label>
                                    <input type="text" value={form.child_attributes.education.major}
                                        onChange={e => updateChildAttr('education.major', e.target.value)}
                                        className={inputClass('child_attributes.education.major')}
                                        placeholder="Major (optional)" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NISN</label>
                                    <input type="text" value={form.child_attributes.education.nisn}
                                        onChange={e => updateChildAttr('education.nisn', e.target.value)}
                                        className={inputClass('child_attributes.education.nisn')}
                                        placeholder="10-digit NISN (optional)" />
                                </div>
                            </div>
                        </div>
                    )}
                </section>

                {/* Active Status */}
                <section className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status</h2>
                    <label className="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" checked={form.is_active}
                            onChange={e => update('is_active', e.target.checked)}
                            className="sr-only peer" />
                        <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-300 dark:peer-focus:ring-cyan-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-cyan-600"></div>
                        <span className="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {form.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </label>
                    <p className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        When inactive, the beneficiary will not appear in active lists and reports.
                    </p>
                </section>

                {/* Family Card */}
                <section className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">Family Card</h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Card Number *</label>
                            <input type="text" value={form.family_card.number}
                                onChange={e => updateFC('number', e.target.value)}
                                className={inputClass('family_card.number')} placeholder="Family card number" required />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Head of Family *</label>
                            <input type="text" value={form.family_card.head_of_family_name}
                                onChange={e => updateFC('head_of_family_name', e.target.value)}
                                className={inputClass('family_card.head_of_family_name')} placeholder="Head of family name" required />
                        </div>
                    </div>
                    <h3 className="text-sm font-medium text-gray-600 dark:text-gray-400 mt-4 mb-3">Address</h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div className="md:col-span-2 lg:col-span-4">
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Street</label>
                            <input type="text" value={form.family_card.address.street}
                                onChange={e => updateFC('addr_street', e.target.value)}
                                className={inputClass('family_card.address.street')} placeholder="Street address" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RT</label>
                            <input type="text" value={form.family_card.address.rt}
                                onChange={e => updateFC('addr_rt', e.target.value)}
                                className={inputClass('family_card.address.rt')} placeholder="RT" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RW</label>
                            <input type="text" value={form.family_card.address.rw}
                                onChange={e => updateFC('addr_rw', e.target.value)}
                                className={inputClass('family_card.address.rw')} placeholder="RW" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Village</label>
                            <input type="text" value={form.family_card.address.village}
                                onChange={e => updateFC('addr_village', e.target.value)}
                                className={inputClass('family_card.address.village')} placeholder="Village" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">District</label>
                            <input type="text" value={form.family_card.address.district}
                                onChange={e => updateFC('addr_district', e.target.value)}
                                className={inputClass('family_card.address.district')} placeholder="District" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                            <input type="text" value={form.family_card.address.city}
                                onChange={e => updateFC('addr_city', e.target.value)}
                                className={inputClass('family_card.address.city')} placeholder="City" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Province</label>
                            <input type="text" value={form.family_card.address.province}
                                onChange={e => updateFC('addr_province', e.target.value)}
                                className={inputClass('family_card.address.province')} placeholder="Province" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postal Code</label>
                            <input type="text" value={form.family_card.address.postal_code}
                                onChange={e => updateFC('addr_postal_code', e.target.value)}
                                className={inputClass('family_card.address.postal_code')} placeholder="Postal code" />
                        </div>
                    </div>
                </section>

                {/* Guardians */}
                <section className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div className="flex items-center justify-between mb-4">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-white">Guardians</h2>
                        <button type="button" onClick={addGuardian}
                            className="inline-flex items-center gap-1 text-sm font-medium text-cyan-600 hover:text-cyan-800 dark:text-cyan-400 dark:hover:text-cyan-300">
                            <HiOutlinePlus className="w-4 h-4" /> Add Guardian
                        </button>
                    </div>
                    {form.guardians.map((guardian, idx) => (
                        <div key={idx} className="mb-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600 relative">
                            <div className="flex items-center justify-between mb-3">
                                <span className="text-sm font-medium text-gray-600 dark:text-gray-400">Guardian {idx + 1}</span>
                                {form.guardians.length > 1 && (
                                    <button type="button" onClick={() => removeGuardian(idx)}
                                        className="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        <HiOutlineTrash className="w-4 h-4" />
                                    </button>
                                )}
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                                    <input type="text" value={guardian.person.name}
                                        onChange={e => updateGuardian(idx, 'person.name', e.target.value)}
                                        className={inputClass(`guardians.${idx}.person.name`)}
                                        placeholder="Guardian name" required />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Relationship *</label>
                                    <select value={guardian.relationship}
                                        onChange={e => updateGuardian(idx, 'relationship', e.target.value)}
                                        className={inputClass(`guardians.${idx}.relationship`)} required>
                                        <option value="father">Father</option>
                                        <option value="mother">Mother</option>
                                        <option value="grandfather">Grandfather</option>
                                        <option value="grandmother">Grandmother</option>
                                        <option value="uncle">Uncle</option>
                                        <option value="aunt">Aunt</option>
                                        <option value="sibling">Sibling</option>
                                        <option value="foster_parent">Foster Parent</option>
                                        <option value="legal_guardian">Legal Guardian</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Occupation</label>
                                    <input type="text" value={guardian.person.occupation}
                                        onChange={e => updateGuardian(idx, 'person.occupation', e.target.value)}
                                        className={inputClass(`guardians.${idx}.person.occupation`)}
                                        placeholder="Occupation (optional)" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Education</label>
                                    <select value={guardian.person.education}
                                        onChange={e => updateGuardian(idx, 'person.education', e.target.value)}
                                        className={inputClass(`guardians.${idx}.person.education`)}>
                                        <option value="">- Not specified -</option>
                                        <option value="none">None</option>
                                        <option value="elementary">Elementary</option>
                                        <option value="junior_high">Junior High</option>
                                        <option value="senior_high">Senior High</option>
                                        <option value="diploma">Diploma</option>
                                        <option value="bachelor">Bachelor</option>
                                        <option value="master">Master</option>
                                        <option value="doctor">Doctor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    ))}
                </section>

                {/* Actions */}
                <div className="flex items-center gap-4">
                    <button type="submit" disabled={loading}
                        className="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-700 disabled:bg-cyan-400 text-white font-medium rounded-lg transition-colors">
                        {loading ? 'Saving...' : (isEdit ? 'Update Beneficiary' : 'Save Beneficiary')}
                    </button>
                    <button type="button" onClick={() => navigate('/beneficiaries')}
                        className="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    );
}
