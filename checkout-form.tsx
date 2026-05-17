"use client";

import { FormEvent, useEffect, useState } from "react";
import { useRouter } from "next/navigation";

type CartItem = { id: number; quantity: number };

export function CheckoutForm() {
  const router = useRouter();
  const [items, setItems] = useState<CartItem[]>([]);
  const [error, setError] = useState("");

  useEffect(() => {
    setItems(JSON.parse(localStorage.getItem("flores_alesli_cart") ?? "[]"));
  }, []);

  async function submit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setError("");
    const form = new FormData(event.currentTarget);
    const shippingAddress = String(form.get("address") ?? "");
    const response = await fetch("/api/orders", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        customer: {
          name: form.get("name"),
          email: form.get("email"),
          phone: form.get("phone"),
          address: shippingAddress,
          notes: form.get("notes")
        },
        items: items.map((item) => ({ productId: item.id, quantity: item.quantity })),
        paymentMethod: form.get("paymentMethod"),
        shippingAddress,
        coupon: form.get("coupon"),
        customMessage: form.get("customMessage"),
        deliveryDate: form.get("deliveryDate")
      })
    });
    const payload = await response.json();
    if (!response.ok) {
      setError(payload.error ?? "No se pudo crear el pedido");
      return;
    }
    localStorage.removeItem("flores_alesli_cart");
    router.push(`/pedidos/${payload.data.id}`);
  }

  return (
    <form className="card card-body" onSubmit={submit}>
      <div className="form-grid">
        <label className="field"><span>Nombre</span><input name="name" required /></label>
        <label className="field"><span>Email</span><input name="email" type="email" required /></label>
        <label className="field"><span>Telefono</span><input name="phone" required /></label>
        <label className="field"><span>Direccion de entrega</span><input name="address" required /></label>
        <label className="field"><span>Fecha de entrega</span><input name="deliveryDate" type="datetime-local" /></label>
        <label className="field"><span>Metodo de pago</span><select name="paymentMethod"><option>QR</option><option>Efectivo</option><option>Tarjeta</option></select></label>
        <label className="field"><span>Cupon</span><input name="coupon" placeholder="ALESLI10" /></label>
      </div>
      <label className="field"><span>Mensaje personalizado</span><textarea name="customMessage" rows={3} /></label>
      <label className="field"><span>Notas del pedido</span><textarea name="notes" rows={3} /></label>
      {error ? <p className="notice">{error}</p> : null}
      <button className="button" disabled={!items.length}>Crear pedido</button>
    </form>
  );
}
